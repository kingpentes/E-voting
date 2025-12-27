import Web3 from "web3";
import fs from "fs";

// Ambil parameter dari command line
const [contractAddress, electionId] = process.argv.slice(2);

// Validasi parameter
if (!contractAddress || !electionId) {
  console.error("Usage: node getVoteCount.js <contractAddress> <electionId>");
  process.exit(1);
}

const rpc =
  process.env.RPC || process.env.BLOCKCHAIN_RPC || "http://127.0.0.1:18545";
const abiPath =
  process.env.ABI_OUTPUT_PATH ||
  process.env.CONTRACT_ABI_PATH ||
  "D:/E-voting/storage/contract/evote_abi.json";

const web3 = new Web3(rpc);
const abi = JSON.parse(fs.readFileSync(abiPath, "utf8"));
const contract = new web3.eth.Contract(abi, contractAddress);

/**
 * AMBIL JUMLAH VOTE
 * Menggunakan method .call() karena hanya membaca data (tidak mengubah state)
 * Tidak memerlukan gas karena hanya read operation
 */
try {
  const count = await contract.methods.getVoteCount(electionId).call();
  console.log(count.toString());
} catch (error) {
  console.error("Error:", error.message);
  process.exit(1);
}
