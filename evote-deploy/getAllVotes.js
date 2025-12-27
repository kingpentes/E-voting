import Web3 from 'web3';
import fs from 'fs';

// Ambil parameter dari command line
const [contractAddress, electionId] = process.argv.slice(2);

// Validasi parameter
if (!contractAddress || !electionId) {
    console.error('Usage: node getAllVotes.js <contractAddress> <electionId>');
    process.exit(1);
}

// Koneksi ke blockchain Quorum
const web3 = new Web3('http://127.0.0.1:18545');

// Load ABI contract
const abiPath = 'C:/laragon/www/smart-voting/storage/contract/evote_abi.json';
const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));

// Inisialisasi contract instance
const contract = new web3.eth.Contract(abi, contractAddress);

/**
 * Konversi hex ke base64 untuk kompatibilitas dengan PHP decrypt
 */
function hexToBase64(hex) {
    if (!hex || hex === '0x') return '';
    const cleanHex = hex.startsWith('0x') ? hex.slice(2) : hex;
    if (cleanHex.length === 0) return '';
    return Buffer.from(cleanHex, 'hex').toString('base64');
}

try {
    // 1. Ambil jumlah total vote
    const count = await contract.methods.getVoteCount(electionId).call();
    const voteCount = Number(count);

    // 2. Jika tidak ada vote, return array kosong
    if (voteCount === 0) {
        console.log(JSON.stringify([]));
        process.exit(0);
    }

    // 3. Ambil semua vote satu per satu
    const votes = [];
    for (let i = 0; i < voteCount; i++) {
        const vote = await contract.methods.getVote(electionId, i).call();

        // Format hasil sesuai return dari smart contract:
        // (ciphertext, nonce, tag, hash, voterId, timestamp)
        votes.push({
            ciphertext: hexToBase64(vote[0]),
            nonce: hexToBase64(vote[1]),
            tag: hexToBase64(vote[2]),
            hash: vote[3],
            voterId: vote[4],
            timestamp: Number(vote[5])
        });
    }

    // 4. Output sebagai JSON
    console.log(JSON.stringify(votes));

} catch (error) {
    console.error('Error:', error.message);
    process.exit(1);
}
