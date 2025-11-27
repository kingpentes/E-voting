/**
 * GET VOTE COUNT SCRIPT
 * =====================
 * Script untuk mengambil jumlah total vote dari blockchain
 * 
 * Fungsi utama:
 * - Mengquery smart contract untuk mendapatkan jumlah vote
 * - Digunakan untuk statistik dan verifikasi data
 * 
 * Parameter yang dibutuhkan:
 * 1. contractAddress - Alamat smart contract di blockchain
 * 2. electionId - ID pemilihan yang ingin dicek
 */

import Web3 from 'web3';
import fs from 'fs';

// Ambil parameter dari command line
const [contractAddress, electionId] = process.argv.slice(2);

// Validasi parameter
if (!contractAddress || !electionId) {
    console.error('Usage: node getVoteCount.js <contractAddress> <electionId>');
    process.exit(1);
}

/**
 * KONEKSI KE BLOCKCHAIN
 * Menghubungkan ke node Quorum di localhost
 */
const web3 = new Web3('http://127.0.0.1:18545');

// Load ABI contract
const abiPath = 'C:/laragon/www/smart-voting/storage/contract/evote_abi.json';
const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));

// Inisialisasi contract instance
const contract = new web3.eth.Contract(abi, contractAddress);

/**
 * AMBIL JUMLAH VOTE
 * Menggunakan method .call() karena hanya membaca data (tidak mengubah state)
 * Tidak memerlukan gas karena hanya read operation
 */
try {
    // Panggil fungsi getVoteCount dari smart contract
    const count = await contract.methods.getVoteCount(electionId).call();

    // Output jumlah vote sebagai string
    console.log(count.toString());

} catch (error) {
    // Handle error dan exit dengan kode 1
    console.error('Error:', error.message);
    process.exit(1);
}
