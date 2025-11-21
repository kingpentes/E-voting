#!/usr/bin/env python3
import os
import sys
import json
import argparse
from web3 import Web3
from web3.middleware import geth_poa_middleware


def connect_web3(rpc_url: str) -> Web3:
    w3 = Web3(Web3.HTTPProvider(rpc_url, request_kwargs={"timeout": 10}))
    w3.middleware_onion.inject(geth_poa_middleware, layer=0)
    if not w3.is_connected():
        raise RuntimeError(f"Failed to connect to RPC at {rpc_url}")
    return w3


def load_contract(w3: Web3, address: str, abi_path: str):
    if not address:
        raise SystemExit("CONTRACT_ADDRESS is required (env or --address)")
    if not abi_path or not os.path.isfile(abi_path):
        raise SystemExit("ABI file path is required and must exist (CONTRACT_ABI_PATH or --abi)")
    with open(abi_path, "r", encoding="utf-8") as f:
        # ABI may be pure array, or a full artifact {"abi": [...]} – handle both
        data = json.load(f)
    abi = data.get("abi", data)
    return w3.eth.contract(address=Web3.to_checksum_address(address), abi=abi)


def main():
    parser = argparse.ArgumentParser(description="Read from EVote contract")
    parser.add_argument("electionId", help="Election id string")
    parser.add_argument("--rpc", default=os.getenv("RPC_URL", "http://localhost:18545"), help="RPC URL")
    parser.add_argument("--address", default=os.getenv("CONTRACT_ADDRESS"), help="Contract address")
    parser.add_argument("--abi", dest="abi_path", default=os.getenv("CONTRACT_ABI_PATH"), help="Path to ABI JSON")
    args = parser.parse_args()

    w3 = connect_web3(args.rpc)
    contract = load_contract(w3, args.address, args.abi_path)

    # Call getVoteCount(electionId)
    count = contract.functions.getVoteCount(args.electionId).call()
    print(json.dumps({"electionId": args.electionId, "count": int(count)}, indent=2))


if __name__ == "__main__":
    main()
