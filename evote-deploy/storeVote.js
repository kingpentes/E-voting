/**
 * STORE VOTE SCRIPT
 * =================
 * Script untuk menyimpan vote yang terenkripsi ke blockchain Quorum
 * 
 * Fungsi utama:
 * - Menerima data vote terenkripsi dari Laravel
 * - Mengkonversi format data dari base64 ke hexadecimal
 * - Mengirim transaksi ke smart contract di blockchain
 * 
 * Parameter yang dibutuhkan:
 * 1. contractAddress - Alamat smart contract di blockchain
 * 2. cipherB64 - Data vote terenkripsi dalam format base64
 * 3. nonceB64 - Nonce untuk enkripsi dalam format base64
 * 4. tagB64 - Authentication tag dalam format base64
 * 5. hashHex - Hash dari data vote dalam format hexadecimal
 * 6. electionId - ID pemilihan
 * 7. voterId - ID voter
 */

import Web3 from 'web3';
import fs from 'fs';

// Ambil parameter dari command line arguments
const [contractAddress, cipherB64, nonceB64, tagB64, hashHex, electionId, voterId] = process.argv.slice(2);

// Validasi: Pastikan semua parameter diperlukan tersedia
if (!contractAddress || !cipherB64 || !nonceB64 || !tagB64 || !hashHex || !electionId || !voterId) {
    console.error('Usage: node storeVote.js <contractAddress> <cipherB64> <nonceB64> <tagB64> <hashHex> <electionId> <voterId>');
    process.exit(1);
}

/**
 * KONVERSI DATA FORMAT
 * Blockchain Quorum memerlukan data dalam format hexadecimal dengan prefix '0x'
 * Data dari Laravel dikirim dalam format base64, sehingga perlu dikonversi
 */

// Konversi ciphertext dari base64 ke hex
const ciphertextHex = '0x' + Buffer.from(cipherB64, 'base64').toString('hex');

// Konversi nonce dari base64 ke hex
const nonceHex = '0x' + Buffer.from(nonceB64, 'base64').toString('hex');

// Konversi authentication tag dari base64 ke hex
const tagHex = '0x' + Buffer.from(tagB64, 'base64').toString('hex');

// Pastikan hash memiliki prefix 0x dan panjang 32 bytes
const hash32 = hashHex.startsWith('0x') ? hashHex : '0x' + hashHex;

/**
 * KONEKSI KE BLOCKCHAIN
 * Menghubungkan ke node Quorum yang berjalan di localhost port 18545
 */
const web3 = new Web3('http://127.0.0.1:18545');

// Load ABI (Application Binary Interface) contract dari file JSON
// ABI berisi informasi tentang fungsi-fungsi yang tersedia di smart contract
const abiPath = 'C:/laragon/www/smart-voting/storage/contract/evote_abi.json';
const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));

// Inisialisasi instance contract dengan alamat dan ABI
const contract = new web3.eth.Contract(abi, contractAddress);

/**
 * KONFIGURASI TRANSAKSI
 */
// Alamat akun yang akan mengirim transaksi (node 1 di Quorum network)
const from = '0xed9d02e382b34818e88b88a309c7fe71e65f419d';

// Gas limit untuk transaksi (8000000 dalam hex)
const gas = 0x7a1200;

/**
 * EKSEKUSI TRANSAKSI
 * Memanggil fungsi storeVote di smart contract
 */
try {
    // Kirim transaksi untuk menyimpan vote
    const receipt = await contract.methods
        .storeVote(ciphertextHex, nonceHex, tagHex, hash32, electionId, voterId)
        .send({
            from: from,      // Akun pengirim
            gas: gas,        // Gas limit
            gasPrice: '0'    // Gas price 0 karena ini private blockchain
        });

    // Output transaction hash untuk tracking di Laravel
    console.log(receipt.transactionHash);

} catch (error) {
    // Tangani error dan exit dengan kode 1 (error)
    console.error('Error:', error.message);
    process.exit(1);
}
