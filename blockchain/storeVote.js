import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import Web3 from "web3";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const RPC = process.env.RPC || process.env.BLOCKCHAIN_RPC || "http://127.0.0.1:18545";
const FROM = (process.env.DEPLOY_FROM || "0xed9d02e382b34818e88b88a309c7fe71e65f419d").toLowerCase();
const ABI_PATH = process.env.ABI_OUTPUT_PATH || process.env.CONTRACT_ABI_PATH;

// Arguments: contractAddress, cipherB64, nonceB64, tagB64, hash32, electionId, voterId
const contractAddress = process.argv[2];
const cipherB64 = process.argv[3];
const nonceB64 = process.argv[4];
const tagB64 = process.argv[5];
const hash32 = process.argv[6];
const electionId = process.argv[7];
const voterId = process.argv[8];

if (!contractAddress || !cipherB64 || !nonceB64 || !tagB64 || !hash32 || !electionId || !voterId) {
    console.error("Usage: node storeVote.js <contractAddress> <cipherB64> <nonceB64> <tagB64> <hash32> <electionId> <voterId>");
    process.exit(1);
}

function b64ToHex(b64) {
    const buffer = Buffer.from(b64, 'base64');
    return '0x' + buffer.toString('hex');
}

async function storeVote() {
    try {
        // Load ABI
        let abiPath = ABI_PATH;
        if (!abiPath || !fs.existsSync(abiPath)) {
            abiPath = path.join(__dirname, "..", "storage", "contract", "evote_abi.json");
        }

        if (!fs.existsSync(abiPath)) {
            console.error("ABI file not found at:", abiPath);
            process.exit(1);
        }

        const abi = JSON.parse(fs.readFileSync(abiPath, "utf8"));

        // Connect to blockchain
        const web3 = new Web3(RPC);
        const contract = new web3.eth.Contract(abi, contractAddress);

        // Convert base64 to hex
        const cipherHex = b64ToHex(cipherB64);
        const nonceHex = b64ToHex(nonceB64);
        const tagHex = b64ToHex(tagB64);

        // Unlock account if needed
        try {
            if (web3.eth.personal && web3.eth.personal.unlockAccount) {
                await web3.eth.personal.unlockAccount(FROM, "", 600);
            }
        } catch (e) {
            // Ignore unlock errors
        }

        // Store vote
        const receipt = await contract.methods
            .storeVote(cipherHex, nonceHex, tagHex, hash32, electionId, voterId)
            .send({ from: FROM, gas: 500000, gasPrice: "0x0", type: "0x0" });

        // Output transaction hash
        console.log(receipt.transactionHash);

    } catch (error) {
        console.error("Error:", error.message);
        process.exit(1);
    }
}

storeVote();
