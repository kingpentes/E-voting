import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import solc from "solc";
import Web3 from "web3";

const RPC = process.env.RPC || "http://127.0.0.1:18545";
const FROM = (
  process.env.DEPLOY_FROM || "0xed9d02e382b34818e88b88a309c7fe71e65f419d"
).toLowerCase();
const GAS_PRICE = process.env.DEPLOY_GAS_PRICE || "0x0";

/**
 * PATH SETUP
 * Mendapatkan path absolut ke file contract
 */
const __dirname = path.dirname(fileURLToPath(import.meta.url));
const contractPath = path.join(__dirname, "contracts", "EvoteEncrypted.sol");
const contractSource = fs.readFileSync(contractPath, "utf8");

/**
 * COMPILER INPUT
 * Konfigurasi untuk Solidity compiler
 */
const input = {
  language: "Solidity",
  sources: {
    "EvoteEncrypted.sol": { content: contractSource },
  },
  settings: {
    evmVersion: "istanbul",
    optimizer: { enabled: true, runs: 200 },
    outputSelection: {
      "*": { "*": ["abi", "evm.bytecode.object"] },
    },
  },
};

/**
 * FUNGSI COMPILE CONTRACT
 * Mengcompile Solidity code menjadi bytecode dan ABI
 * 
 * @returns {Object} { abi, bytecode }
 */
function compile() {
  // Compile contract
  const output = JSON.parse(solc.compile(JSON.stringify(input)));

  // Check untuk compilation errors
  if (output.errors) {
    const fatal = output.errors.filter((e) => e.severity === "error");
    if (fatal.length > 0) {
      console.error(fatal);
      throw new Error("Compilation failed");
    }
  }
  const c = output.contracts["EvoteEncrypted.sol"]["EvoteEncrypted"];
  return { abi: c.abi, bytecode: "0x" + c.evm.bytecode.object };
}

/**
 * FUNGSI DEPLOY CONTRACT
 * Men-deploy compiled contract ke blockchain
 */
async function deploy() {
  // Compile contract terlebih dahulu
  const { abi, bytecode } = compile();

  // Koneksi ke blockchain
  const web3 = new Web3(RPC);
  console.log("Bytecode size (bytes):", (bytecode.length - 2) / 2);

  /**
   * UNLOCK ACCOUNT (opsional)
   * Unlock akun pengirim jika diperlukan
   */
  try {
    if (web3.eth.personal && web3.eth.personal.unlockAccount) {
      await web3.eth.personal.unlockAccount(FROM, "", 600);
    }
  } catch (e) {
    console.warn("unlockAccount warning:", e.message);
  }

  /**
   * PERSIAPAN DEPLOYMENT
   */
  // Buat instance contract dengan ABI
  const contract = new web3.eth.Contract(abi);

  // Siapkan deployment transaction
  const deployTx = contract.deploy({ data: bytecode });

  const latest = await web3.eth.getBlock("latest");
  const blockGasLimit = BigInt(latest.gasLimit || latest.gas || "0");
  console.log("Latest block gasLimit:", blockGasLimit.toString());

  /**
   * DEPLOYMENT DENGAN MULTIPLE GAS CANDIDATES
   * Mencoba deploy dengan berbagai nilai gas limit
   * Mulai dari yang kecil (3M), sedang (5M), hingga besar (8M)
   */
  const candidates = [0x2dc6c0, 0x4c4b40, 0x7a1200];  // 3M, 5M, 8M
  let instance = null;
  let lastErr = null;

  for (const g of candidates) {
    try {
      const gasToUse =
        blockGasLimit > 0n
          ? Number(BigInt(Math.min(g, Number(blockGasLimit - 1000n))))
          : g;
      console.log("Attempt deploy with gas =", gasToUse);
      instance = await deployTx
        .send({ from: FROM, gas: gasToUse, gasPrice: GAS_PRICE, type: "0x0" })
        .on("transactionHash", (tx) => console.log("txHash:", tx));
      if (instance?.options?.address) break;

    } catch (e) {
      lastErr = e;
      console.warn("Attempt failed with gas", g, e.message || e);
    }
  }

  /**
   * VALIDASI DEPLOYMENT
   */
  if (!instance?.options?.address) {
    throw lastErr || new Error("Deployment failed for all gas candidates");
  }

  console.log("contractAddress:", instance.options.address);

  // Write ABI into the Laravel app storage path
  const abiOut =
    process.env.ABI_OUTPUT_PATH ||
    "D://E-voting//storage//contract//evote_abi.json";
  try {
    const dir = path.dirname(abiOut);
    if (!fs.existsSync(dir)) {
      fs.mkdirSync(dir, { recursive: true });
    }
    fs.writeFileSync(abiOut, JSON.stringify(abi, null, 2));
    console.log("ABI written to", abiOut);
  } catch (e) {
    console.warn("Failed to write ABI to Laravel storage path:", e.message);
  }
}

/**
 * EKSEKUSI DEPLOYMENT
 * Jalankan fungsi deploy dan handle errors
 */
deploy().catch((e) => {
  console.error("Deploy error:", e);
  process.exit(1);
});
