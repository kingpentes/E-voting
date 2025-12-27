import Web3 from "web3";
import fs from "fs";
import crypto from "crypto";

const [contractAddress, electionId, candidateId] = process.argv.slice(2);

if (!contractAddress || !electionId || !candidateId) {
  console.error("Usage: node getVotesForCandidate.js <contractAddress> <electionId> <candidateId>");
  process.exit(1);
}

const rpc =
  process.env.RPC || process.env.BLOCKCHAIN_RPC || "http://127.0.0.1:18545";
const abiPath =
  process.env.ABI_OUTPUT_PATH ||
  process.env.CONTRACT_ABI_PATH ||
  "D:/E-voting/storage/contract/evote_abi.json";

// Get encryption key from environment - try VOTE_ENC_KEY first, then APP_KEY
const encryptionKey = process.env.VOTE_ENC_KEY || process.env.APP_KEY;
if (!encryptionKey) {
  console.error("VOTE_ENC_KEY or APP_KEY not set");
  process.exit(1);
}

// Derive 32-byte key from Laravel key (remove "base64:" prefix if present)
const keyBase64 = encryptionKey.replace(/^base64:/, '');
const key = Buffer.from(keyBase64, 'base64');

if (key.length !== 32) {
  console.error('Encryption key must be 32 bytes, got ' + key.length);
  process.exit(1);
}

const web3 = new Web3(rpc);
const abi = JSON.parse(fs.readFileSync(abiPath, "utf8"));
const contract = new web3.eth.Contract(abi, contractAddress);

// Decrypt function compatible with PHP's openssl_decrypt (AES-256-GCM)
function decryptVote(ciphertextB64, nonceB64, tagB64) {
  try {
    const ciphertext = Buffer.from(ciphertextB64, 'base64');
    const nonce = Buffer.from(nonceB64, 'base64');
    const tag = Buffer.from(tagB64, 'base64');
    
    const decipher = crypto.createDecipheriv('aes-256-gcm', key, nonce);
    decipher.setAuthTag(tag);
    
    let decrypted = decipher.update(ciphertext);
    decrypted = Buffer.concat([decrypted, decipher.final()]);
    
    return decrypted.toString('utf8');
  } catch (err) {
    // Decryption failed
    return null;
  }
}

try {
  // Get total vote count for the election
  const totalVotes = await contract.methods.getVoteCount(electionId).call();
  let candidateVotes = 0;

  // Iterate through all votes and count votes for this candidate
  for (let i = 0; i < totalVotes; i++) {
    try {
      const vote = await contract.methods.getVote(electionId, i).call();
      
      // vote[0] = ciphertext (bytes)
      // vote[1] = nonce (bytes)
      // vote[2] = tag (bytes)
      
      // Convert hex to base64
      const ciphertextHex = vote[0].startsWith('0x') ? vote[0].slice(2) : vote[0];
      const nonceHex = vote[1].startsWith('0x') ? vote[1].slice(2) : vote[1];
      const tagHex = vote[2].startsWith('0x') ? vote[2].slice(2) : vote[2];
      
      const ciphertextB64 = Buffer.from(ciphertextHex, 'hex').toString('base64');
      const nonceB64 = Buffer.from(nonceHex, 'hex').toString('base64');
      const tagB64 = Buffer.from(tagHex, 'hex').toString('base64');
      
      // Decrypt the vote
      const decryptedCandidateId = decryptVote(ciphertextB64, nonceB64, tagB64);
      
      // Check if this vote is for the target candidate
      if (decryptedCandidateId && decryptedCandidateId === candidateId) {
        candidateVotes++;
      }
    } catch (err) {
      // Skip votes that can't be read or decrypted
      continue;
    }
  }

  console.log(candidateVotes.toString());
} catch (error) {
  console.error("Error:", error.message);
  process.exit(1);
}
