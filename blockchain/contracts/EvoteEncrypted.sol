// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * ================================
 * EVOTE ENCRYPTED SMART CONTRACT
 * ================================
 * 
 * Smart contract untuk menyimpan vote yang terenkripsi di blockchain Quorum
 * 
 * FITUR KEAMANAN:
 * - Vote disimpan dalam bentuk terenkripsi (AES-256-GCM)
 * - Menggunakan authentication tag untuk validasi
 * - Hash untuk verifikasi integritas data
 * - Immutable dan transparent (blockchain properties)
 * 
 * ARSITEKTUR:
 * - Vote dikelompokkan berdasarkan electionId
 * - Setiap vote menyimpan metadata termasuk timestamp
 * - Event logging untuk audit trail
 */

contract EvoteEncrypted {
    
    /**
     * STRUKTUR DATA VOTE
     * Menyimpan semua informasi yang diperlukan untuk vote terenkripsi
     */
    struct Vote {
        bytes ciphertext;   // Data vote terenkripsi menggunakan AES-256-GCM
        bytes nonce;        // Nonce 12-byte untuk dekripsi (GCM mode)
        bytes tag;          // Authentication tag 16-byte untuk validasi
        bytes32 hash;       // Hash SHA-256 dari plaintext untuk verifikasi integritas
        string electionId;  // ID pemilihan (untuk grouping)
        string voterId;     // ID voter (untuk tracking tanpa mengungkap pilihan)
        uint256 timestamp;  // Timestamp ketika vote disimpan (block.timestamp)
    }

    /**
     * STATE VARIABLES
     */
    // Alamat owner contract (yang men-deploy)
    address public owner;
    
    // Mapping untuk menyimpan votes berdasarkan election
    // Key: keccak256(electionId) untuk efisiensi gas
    // Value: Array dari Vote struct
    mapping(bytes32 => Vote[]) private votesByElection;

    /**
     * EVENTS
     * Event untuk logging dan audit trail
     */
    event VoteStored(
        string indexed electionId,  // ID pemilihan (indexed untuk filtering)
        string indexed voterId,     // ID voter (indexed untuk filtering)
        bytes32 indexed hash,       // Hash vote (indexed untuk filtering)
        address sender,             // Alamat yang mengirim transaksi
        uint256 idx,                // Index vote dalam array
        uint256 timestamp           // Timestamp penyimpanan
    );

    /**
     * MODIFIERS
     */
    // Modifier untuk membatasi akses hanya untuk owner
    modifier onlyOwner() {
        require(msg.sender == owner, "only owner");
        _;
    }

    /**
     * CONSTRUCTOR
     * Dipanggil sekali saat deployment
     * Set msg.sender sebagai owner
     */
    constructor() {
        owner = msg.sender;
    }

    /**
     * STORE VOTE FUNCTION
     * ==================
     * Fungsi untuk menyimpan vote terenkripsi ke blockchain
     * 
     * Parameters:
     * @param ciphertext - Vote terenkripsi (bytes)
     * @param nonce - Nonce 12-byte untuk AES-GCM
     * @param tag - Authentication tag 16-byte
     * @param hash - Hash SHA-256 dari plaintext vote
     * @param electionId - ID pemilihan
     * @param voterId - ID voter
     * 
     * Catatan:
     * - Fungsi ini PUBLIC dan bisa dipanggil oleh siapa saja
     * - Menggunakan calldata untuk efisiensi gas (data tidak di-copy ke memory)
     * - Timestamp otomatis di-set ke block.timestamp
     */
    function storeVote(
        bytes calldata ciphertext,
        bytes calldata nonce,
        bytes calldata tag,
        bytes32 hash,
        string calldata electionId,
        string calldata voterId
    ) external {
        // Generate key untuk mapping (hash dari electionId)
        bytes32 key = keccak256(bytes(electionId));
        
        // Tambahkan vote baru ke array
        votesByElection[key].push(Vote({
            ciphertext: ciphertext,
            nonce: nonce,
            tag: tag,
            hash: hash,
            electionId: electionId,
            voterId: voterId,
            timestamp: block.timestamp  // Gunakan block timestamp
        }));
        
        // Emit event untuk logging
        emit VoteStored(
            electionId, 
            voterId, 
            hash, 
            msg.sender, 
            votesByElection[key].length - 1,  // Index vote yang baru disimpan
            block.timestamp
        );
    }

    /**
     * GET VOTE COUNT FUNCTION
     * ======================
     * Mendapatkan jumlah total vote untuk suatu pemilihan
     * 
     * @param electionId - ID pemilihan
     * @return uint256 - Jumlah vote
     * 
     * Catatan:
     * - View function (tidak mengubah state, tidak memerlukan gas)
     * - Return panjang array votes untuk election tertentu
     */
    function getVoteCount(string calldata electionId) external view returns (uint256) {
        return votesByElection[keccak256(bytes(electionId))].length;
    }

    /**
     * GET VOTE FUNCTION
     * ================
     * Mengambil data vote tertentu berdasarkan index
     * 
     * @param electionId - ID pemilihan
     * @param index - Index vote dalam array (0-based)
     * @return ciphertext - Data vote terenkripsi
     * @return nonce - Nonce untuk dekripsi
     * @return tag - Authentication tag
     * @return hash - Hash untuk verifikasi
     * @return voterId - ID voter
     * @return timestamp - Waktu penyimpanan
     * 
     * Catatan:
     * - View function (tidak memerlukan gas)
     * - Menggunakan storage karena hanya membaca reference
     * - Akan revert jika index out of bounds
     */
    function getVote(
        string calldata electionId,
        uint256 index
    ) external view returns (
        bytes memory ciphertext,
        bytes memory nonce,
        bytes memory tag,
        bytes32 hash,
        string memory voterId,
        uint256 timestamp
    ) {
        // Ambil reference ke vote di storage
        Vote storage v = votesByElection[keccak256(bytes(electionId))][index];
        
        // Return semua field kecuali electionId (sudah diketahui dari parameter)
        return (
            v.ciphertext, 
            v.nonce, 
            v.tag, 
            v.hash, 
            v.voterId, 
            v.timestamp
        );
    }
}
