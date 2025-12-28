import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import Web3 from "web3";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const RPC = process.env.RPC || process.env.BLOCKCHAIN_RPC || "http://127.0.0.1:18545";
const ABI_PATH = process.env.ABI_OUTPUT_PATH || process.env.CONTRACT_ABI_PATH;

// Arguments: contractAddress, electionId
const contractAddress = process.argv[2];
const electionId = process.argv[3];

if (!contractAddress || !electionId) {
    console.error("Usage: node getVoteCount.js <contractAddress> <electionId>");
    process.exit(1);
}

async function getVoteCount() {
    try {
        // Load ABI
        let abiPath = ABI_PATH;
        if (!abiPath || !fs.existsSync(abiPath)) {
            // Try default path
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

        // Call getVoteCount function
        const count = await contract.methods.getVoteCount(electionId).call();

        // Output just the number
        console.log(count.toString());

    } catch (error) {
        console.error("Error:", error.message);
        process.exit(1);
    }
}

getVoteCount();
