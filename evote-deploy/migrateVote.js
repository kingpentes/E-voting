import Web3 from 'web3';
import fs from 'fs';
import crypto from 'crypto';

const [contractAddress, electionId, voterId, candidateId, encKey] = process.argv.slice(2);

if (!contractAddress || !electionId || !voterId || !candidateId || !encKey) {
    console.error('Usage: node migrateVote.js <contractAddress> <electionId> <voterId> <candidateId> <encKeyBase64>');
    process.exit(1);
}

// Decrypt base64 key
const keyBuffer = encKey.startsWith('base64:') 
    ? Buffer.from(encKey.substring(7), 'base64')
    : Buffer.from(encKey, 'base64');

if (keyBuffer.length !== 32) {
    console.error('Key must be 32 bytes (AES-256)');
    process.exit(1);
}

// Encrypt vote data
const plaintext = candidateId;
const nonce = crypto.randomBytes(12);
const cipher = crypto.createCipheriv('aes-256-gcm', keyBuffer, nonce);

let ciphertext = cipher.update(plaintext, 'utf8');
ciphertext = Buffer.concat([ciphertext, cipher.final()]);
const tag = cipher.getAuthTag();

// Hash
const hash = crypto.createHash('sha256').update(plaintext).digest();

// Convert to hex with 0x prefix
const ciphertextHex = '0x' + ciphertext.toString('hex');
const nonceHex = '0x' + nonce.toString('hex');
const tagHex = '0x' + tag.toString('hex');
const hashHex = '0x' + hash.toString('hex');

// Connect to blockchain
const web3 = new Web3('http://127.0.0.1:18545');
const abiPath = 'C:/laragon/www/smart-voting/storage/contract/evote_abi.json';
const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));
const contract = new web3.eth.Contract(abi, contractAddress);

const from = '0xed9d02e382b34818e88b88a309c7fe71e65f419d';
const gas = 0x7a1200;

try {
    const receipt = await contract.methods
        .storeVote(ciphertextHex, nonceHex, tagHex, hashHex, electionId, voterId)
        .send({ from, gas, gasPrice: '0' });
    
    console.log(receipt.transactionHash);
} catch (error) {
    console.error('Error:', error.message);
    process.exit(1);
}
