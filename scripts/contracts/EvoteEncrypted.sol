// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

contract EvoteEncrypted {
    struct Vote {
        bytes ciphertext; // AES-256-GCM ciphertext
        bytes nonce;      // 12-byte nonce
        bytes tag;        // 16-byte auth tag
        bytes32 hash;     // keccak256(plaintext) or Merkle root
        string electionId;
        string voterId;
        uint256 timestamp;
    }

    address public owner;
    mapping(bytes32 => Vote[]) private votesByElection; // key: keccak256(electionId)

    event VoteStored(
        string indexed electionId,
        string indexed voterId,
        bytes32 indexed hash,
        address sender,
        uint256 idx,
        uint256 timestamp
    );

    modifier onlyOwner() {
        require(msg.sender == owner, "only owner");
        _;
    }

    constructor() {
        owner = msg.sender;
    }

    function storeVote(
        bytes calldata ciphertext,
        bytes calldata nonce,
        bytes calldata tag,
        bytes32 hash,
        string calldata electionId,
        string calldata voterId
    ) external {
        bytes32 key = keccak256(bytes(electionId));
        votesByElection[key].push(Vote({
            ciphertext: ciphertext,
            nonce: nonce,
            tag: tag,
            hash: hash,
            electionId: electionId,
            voterId: voterId,
            timestamp: block.timestamp
        }));
        emit VoteStored(electionId, voterId, hash, msg.sender, votesByElection[key].length - 1, block.timestamp);
    }

    function getVoteCount(string calldata electionId) external view returns (uint256) {
        return votesByElection[keccak256(bytes(electionId))].length;
    }

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
        Vote storage v = votesByElection[keccak256(bytes(electionId))][index];
        return (v.ciphertext, v.nonce, v.tag, v.hash, v.voterId, v.timestamp);
    }
}
