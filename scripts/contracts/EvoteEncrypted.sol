// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

/**
 * EvoteEncrypted Smart Contract
 * 
 * Contract untuk menyimpan vote terenkripsi dengan metadata pemilu
 * Mendukung multiple elections dengan election ID
 */
contract EvoteEncrypted {
    
    /**
     * Struct untuk menyimpan vote terenkripsi
     */
    struct Vote {
        bytes ciphertext;      // Vote terenkripsi (base64)
        bytes nonce;           // Nonce untuk dekripsi
        bytes tag;             // Authentication tag
        bytes32 hash;          // Hash dari plaintext untuk verifikasi
        string electionId;     // ID pemilu
        string voterId;        // ID pemilih
        uint256 timestamp;     // Waktu vote disimpan
    }
    
    // Mapping: electionId => array of votes
    mapping(string => Vote[]) private votesByElection;
    
    // Mapping: electionId => candidateId => vote count
    mapping(string => mapping(string => uint256)) private candidateVotes;
    
    // Events
    event VoteStored(
        string indexed electionId,
        string voterId,
        bytes32 hash,
        uint256 timestamp,
        uint256 voteIndex
    );
    
    event CandidateVoteIncremented(
        string indexed electionId,
        string candidateId,
        uint256 newCount
    );
    
    /**
     * Menyimpan vote terenkripsi ke blockchain
     * 
     * @param _ciphertext Vote terenkripsi dalam base64
     * @param _nonce Nonce untuk dekripsi
     * @param _tag Authentication tag
     * @param _hash Hash dari plaintext untuk verifikasi
     * @param _electionId ID pemilu
     * @param _voterId ID pemilih
     */
    function storeVote(
        bytes memory _ciphertext,
        bytes memory _nonce,
        bytes memory _tag,
        bytes32 _hash,
        string memory _electionId,
        string memory _voterId
    ) public {
        Vote memory newVote = Vote({
            ciphertext: _ciphertext,
            nonce: _nonce,
            tag: _tag,
            hash: _hash,
            electionId: _electionId,
            voterId: _voterId,
            timestamp: block.timestamp
        });
        
        votesByElection[_electionId].push(newVote);
        uint256 voteIndex = votesByElection[_electionId].length - 1;
        
        emit VoteStored(_electionId, _voterId, _hash, block.timestamp, voteIndex);
    }
    
    /**
     * Increment vote count untuk kandidat tertentu
     * Digunakan setelah dekripsi vote di backend
     * 
     * @param _electionId ID pemilu
     * @param _candidateId ID kandidat
     */
    function incrementCandidateVote(
        string memory _electionId,
        string memory _candidateId
    ) public {
        candidateVotes[_electionId][_candidateId]++;
        
        emit CandidateVoteIncremented(
            _electionId,
            _candidateId,
            candidateVotes[_electionId][_candidateId]
        );
    }
    
    /**
     * Mendapatkan jumlah total vote untuk election tertentu
     * 
     * @param _electionId ID pemilu
     * @return Jumlah total vote
     */
    function getVoteCount(string memory _electionId) public view returns (uint256) {
        return votesByElection[_electionId].length;
    }
    
    /**
     * Mendapatkan vote berdasarkan election ID dan index
     * 
     * @param _electionId ID pemilu
     * @param _index Index vote dalam array
     * @return Vote struct
     */
    function getVote(string memory _electionId, uint256 _index) 
        public 
        view 
        returns (
            bytes memory ciphertext,
            bytes memory nonce,
            bytes memory tag,
            bytes32 hash,
            string memory electionId,
            string memory voterId,
            uint256 timestamp
        ) 
    {
        require(_index < votesByElection[_electionId].length, "Vote index out of bounds");
        
        Vote memory v = votesByElection[_electionId][_index];
        return (
            v.ciphertext,
            v.nonce,
            v.tag,
            v.hash,
            v.electionId,
            v.voterId,
            v.timestamp
        );
    }
    
    /**
     * Mendapatkan semua vote untuk election tertentu
     * Warning: Gas intensive untuk election dengan banyak vote
     * 
     * @param _electionId ID pemilu
     * @return Array of votes
     */
    function getAllVotes(string memory _electionId) 
        public 
        view 
        returns (Vote[] memory) 
    {
        return votesByElection[_electionId];
    }
    
    /**
     * Mendapatkan jumlah vote untuk kandidat tertentu
     * 
     * @param _electionId ID pemilu
     * @param _candidateId ID kandidat
     * @return Jumlah vote untuk kandidat
     */
    function getVotesForCandidate(
        string memory _electionId,
        string memory _candidateId
    ) public view returns (uint256) {
        return candidateVotes[_electionId][_candidateId];
    }
}
