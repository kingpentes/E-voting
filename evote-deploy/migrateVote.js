/**
 * MIGRATE VOTE SCRIPT
 * ===================
 * Script untuk migrasi vote dari database ke blockchain
 * Digunakan untuk testing atau memindahkan data historis ke blockchain
 * 
 * Fungsi utama:
 * - Melakukan enkripsi vote data menggunakan AES-256-GCM
 * - Membuat hash untuk verifikasi integritas
 * - Menyimpan vote terenkripsi ke blockchain
 * 
 * Parameter yang dibutuhkan:
 * 1. contractAddress - Alamat smart contract
 * 2. electionId - ID pemilihan
 * 3. voterId - ID voter
 * 4. candidateId - ID kandidat yang dipilih
 * 5. encKeyBase64 - Encryption key dalam format base64
 */

import Web3 from 'web3';
import fs from 'fs';
import crypto from 'crypto';

// Ambil parameter dari command line
const [contractAddress, electionId, voterId, candidateId, encKey] = process.argv.slice(2);

// Validasi parameter
if (!contractAddress || !electionId || !voterId || !candidateId || !encKey) {
    console.error('Usage: node migrateVote.js <contractAddress> <electionId> <voterId> <candidateId> <encKeyBase64>');
    process.exit(1);
}

/**
 * DECODE ENCRYPTION KEY
 * Laravel APP_KEY biasanya dalam format "base64:..." atau langsung base64
 */
const keyBuffer = encKey.startsWith('base64:')
    ? Buffer.from(encKey.substring(7), 'base64')  // Hapus prefix "base64:"
    : Buffer.from(encKey, 'base64');

// Validasi panjang key (harus 32 bytes untuk AES-256)
if (keyBuffer.length !== 32) {
    console.error('Key must be 32 bytes (AES-256)');
    process.exit(1);
}

/**
 * ENKRIPSI VOTE DATA
*/

// Data yang akan dienkripsi (ID kandidat)
const plaintext = candidateId;

// Generate random nonce (12 bytes untuk GCM)
const nonce = crypto.randomBytes(12);

// Buat cipher dengan algorithm AES-256-GCM
const cipher = crypto.createCipheriv('aes-256-gcm', keyBuffer, nonce);

// Enkripsi plaintext
let ciphertext = cipher.update(plaintext, 'utf8');
ciphertext = Buffer.concat([ciphertext, cipher.final()]);

// Dapatkan authentication tag untuk validasi
const tag = cipher.getAuthTag();

/**
 * GENERATE HASH
 * Hash digunakan untuk verifikasi integritas data
 */
const hash = crypto.createHash('sha256').update(plaintext).digest();

/**
 * KONVERSI KE FORMAT HEX
 * Blockchain memerlukan data dalam format hexadecimal dengan prefix 0x
 */
const ciphertextHex = '0x' + ciphertext.toString('hex');
const nonceHex = '0x' + nonce.toString('hex');
const tagHex = '0x' + tag.toString('hex');
const hashHex = '0x' + hash.toString('hex');

/**
 * KONEKSI KE BLOCKCHAIN
 */
const web3 = new Web3('http://127.0.0.1:18545');

// Load ABI contract
const abiPath = 'C:/laragon/www/smart-voting/storage/contract/evote_abi.json';
const abi = JSON.parse(fs.readFileSync(abiPath, 'utf8'));

// Inisialisasi contract instance
const contract = new web3.eth.Contract(abi, contractAddress);

/**
 * KONFIGURASI TRANSAKSI
 */
// Akun pengirim (node 1)
const from = '0xed9d02e382b34818e88b88a309c7fe71e65f419d';

// Gas limit
const gas = 0x7a1200;  // 8000000

/**
 * KIRIM VOTE KE BLOCKCHAIN
 * Menyimpan vote terenkripsi beserta metadata ke smart contract
 */
try {
    const receipt = await contract.methods
        .storeVote(
            ciphertextHex,  // Vote terenkripsi
            nonceHex,       // Nonce untuk dekripsi
            tagHex,         // Authentication tag
            hashHex,        // Hash untuk verifikasi
            electionId,     // ID pemilihan
            voterId         // ID voter
        )
        .send({
            from: from,      // Pengirim
            gas: gas,        // Gas limit
            gasPrice: '0'    // Gas price 0 (private blockchain)
        });

    // Output transaction hash
    console.log(receipt.transactionHash);

} catch (error) {
    // Handle error
    console.error('Error:', error.message);
    process.exit(1);
}
