#!/usr/bin/env python3
import os
import argparse
import json
import requests


def rpc_call(rpc_url: str, method: str, params: list | None = None, timeout: int = 10):
    headers = {"Content-Type": "application/json"}
    payload = {"jsonrpc": "2.0", "id": 1, "method": method, "params": params or []}
    resp = requests.post(rpc_url, headers=headers, data=json.dumps(payload), timeout=timeout)
    resp.raise_for_status()
    data = resp.json()
    if "error" in data:
        raise RuntimeError(f"RPC error: {data['error']}")
    return data.get("result")


def print_node_info(rpc_url: str):
    client_version = rpc_call(rpc_url, "web3_clientVersion")
    net_version = rpc_call(rpc_url, "net_version")
    try:
        chain_id_hex = rpc_call(rpc_url, "eth_chainId")
        chain_id = int(chain_id_hex, 16)
    except Exception:
        chain_id = "<unknown>"
    latest_hex = rpc_call(rpc_url, "eth_blockNumber")
    latest = int(latest_hex, 16)
    print("Connected:")
    print(f"  client_version: {client_version}")
    print(f"  net_version:    {net_version}")
    print(f"  chain_id:       {chain_id}")
    print(f"  latest_block:   {latest}")


def try_send_tx(rpc_url: str, from_addr: str, to_addr: str | None):
    if not from_addr or not from_addr.startswith("0x"):
        raise SystemExit("--from must be a hex address, e.g., 0xabc...")
    if not to_addr:
        to_addr = from_addr
    if not to_addr.startswith("0x"):
        raise SystemExit("--to must be a hex address, e.g., 0xabc...")

    tx = {
        "from": from_addr,
        "to": to_addr,
        "value": hex(0),
        # gas is required by RPC even if GoQuorum ignores pricing
        "gas": hex(21000),
    }
    # If the account is unlocked on the node, this will be signed & broadcast
    tx_hash = rpc_call(rpc_url, "eth_sendTransaction", [tx])
    print(f"Sent tx: {tx_hash}")


def main() -> None:
    parser = argparse.ArgumentParser(description="Simple Quorum RPC connectivity test (JSON-RPC via requests)")
    parser.add_argument("--rpc", default=os.getenv("RPC_URL", "http://localhost:18545"), help="RPC URL")
    parser.add_argument("--send", action="store_true", help="Send a 0-wei tx from an unlocked account")
    parser.add_argument("--from", dest="from_addr", default=os.getenv("FROM_ADDRESS"), help="From address (required with --send)")
    parser.add_argument("--to", dest="to_addr", default=os.getenv("TO_ADDRESS"), help="To address (optional, defaults to --from)")

    args = parser.parse_args()

    print_node_info(args.rpc)

    if args.send:
        if not args.from_addr:
            raise SystemExit("--from is required when using --send (or set FROM_ADDRESS env)")
        try_send_tx(args.rpc, args.from_addr, args.to_addr)


if __name__ == "__main__":
    main()
