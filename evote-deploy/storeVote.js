import Web3 from 'web3';
import fs from 'fs';

const [contractAddress, cipherB64, nonceB64, tagB64, hashHex, electionId, voterId] = process.argv.slice(2);

if (!contractAddress || !cipherB64 || !nonceB64 || !tagB64 || !hashHex || !electionId || !voterId) {
    console.error('Usage: node storeVote.js <contractAddress> <cipherB64> <nonceB64> <tagB64> <hashHex> <electionId> <voterId>');
    process.exit(1);
}

// Convert base64 to hex with 0x prefix
const ciphertextHex = '0x' + Buffer.from(cipherB64, 'base64').toString('hex');
const nonceHex = '0x' + Buffer.from(nonceB64, 'base64').toString('hex');
const tagHex = '0x' + Buffer.from(tagB64, 'base64').toString('hex');

// Ensure hash has 0x prefix and is 32 bytes
const hash32 = hashHex.startsWith('0x') ? hashHex : '0x' + hashHex;

// Connect to blockchain
const web3 = new Web3('http://127.0.0.1:18545');
const abiPath = 'C:/laragon/www/smart-voting/storage/contract/evote_abi.json';
const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));
const contract = new web3.eth.Contract(abi, contractAddress);

const from = '0xed9d02e382b34818e88b88a309c7fe71e65f419d';
const gas = 0x7a1200;

try {
    const receipt = await contract.methods
        .storeVote(ciphertextHex, nonceHex, tagHex, hash32, electionId, voterId)
        .send({ from, gas, gasPrice: '0' });
    
    console.log(receipt.transactionHash);
} catch (error) {
    console.error('Error:', error.message);
    process.exit(1);
}
