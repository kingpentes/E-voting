import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import solc from 'solc';
import Web3 from 'web3';

const RPC = process.env.RPC || 'http://127.0.0.1:18545';
const FROM = (process.env.DEPLOY_FROM || '0xed9d02e382b34818e88b88a309c7fe71e65f419d').toLowerCase();
const GAS_PRICE = process.env.DEPLOY_GAS_PRICE || '0x0';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const contractPath = path.join(__dirname, 'contracts', 'EvoteEncrypted.sol');
const contractSource = fs.readFileSync(contractPath, 'utf8');

const input = {
  language: 'Solidity',
  sources: {
    'EvoteEncrypted.sol': { content: contractSource }
  },
  settings: {
    evmVersion: 'istanbul',
    optimizer: { enabled: true, runs: 200 },
    outputSelection: {
      '*': { '*': ['abi', 'evm.bytecode.object'] }
    }
  }
};

function compile() {
  const output = JSON.parse(solc.compile(JSON.stringify(input)));
  if (output.errors) {
    const fatal = output.errors.filter(e => e.severity === 'error');
    if (fatal.length > 0) {
      console.error(fatal);
      throw new Error('Compilation failed');
    }
  }
  const c = output.contracts['EvoteEncrypted.sol']['EvoteEncrypted'];
  return { abi: c.abi, bytecode: '0x' + c.evm.bytecode.object };
}

async function deploy() {
  const { abi, bytecode } = compile();
  const web3 = new Web3(RPC);
  console.log('Bytecode size (bytes):', (bytecode.length - 2) / 2);

  try {
    if (web3.eth.personal && web3.eth.personal.unlockAccount) {
      await web3.eth.personal.unlockAccount(FROM, '', 600);
    }
  } catch (e) {
    console.warn('unlockAccount warning:', e.message);
  }

  const contract = new web3.eth.Contract(abi);
  const deployTx = contract.deploy({ data: bytecode });

  const latest = await web3.eth.getBlock('latest');
  const blockGasLimit = BigInt(latest.gasLimit || latest.gas || '0');
  console.log('Latest block gasLimit:', blockGasLimit.toString());

  const candidates = [0x2dc6c0, 0x4c4b40, 0x7a1200];
  let instance = null;
  let lastErr = null;
  for (const g of candidates) {
    try {
      const gasToUse = blockGasLimit > 0n ? Number(BigInt(Math.min(g, Number(blockGasLimit - 1000n)))) : g;
      console.log('Attempt deploy with gas =', gasToUse);
      instance = await deployTx.send({ from: FROM, gas: gasToUse, gasPrice: GAS_PRICE, type: '0x0' })
        .on('transactionHash', (tx) => console.log('txHash:', tx));
      if (instance?.options?.address) break;
    } catch (e) {
      lastErr = e;
      console.warn('Attempt failed with gas', g, e.message || e);
    }
  }
  if (!instance?.options?.address) {
    throw lastErr || new Error('Deployment failed for all gas candidates');
  }

  console.log('contractAddress:', instance.options.address);

  // Write ABI into the Laravel app storage path
  const abiOut = 'C://laragon//www//smart-voting//storage//contract//evote_abi.json';
  try {
    fs.writeFileSync(abiOut, JSON.stringify(abi, null, 2));
    console.log('ABI written to', abiOut);
  } catch (e) {
    console.warn('Failed to write ABI to Laravel storage path:', e.message);
  }
}

deploy().catch((e) => {
  console.error('Deploy error:', e);
  process.exit(1);
});
