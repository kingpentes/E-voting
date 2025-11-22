import Web3 from 'web3';
import fs from 'fs';

const [contractAddress, electionId] = process.argv.slice(2);

if (!contractAddress || !electionId) {
    console.error('Usage: node getVoteCount.js <contractAddress> <electionId>');
    process.exit(1);
}

const web3 = new Web3('http://127.0.0.1:18545');
const abiPath = 'C:/laragon/www/smart-voting/storage/contract/evote_abi.json';
const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));
const contract = new web3.eth.Contract(abi, contractAddress);

try {
    const count = await contract.methods.getVoteCount(electionId).call();
    console.log(count.toString());
} catch (error) {
    console.error('Error:', error.message);
    process.exit(1);
}
