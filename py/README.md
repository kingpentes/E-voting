# Quorum Python Quick Tests

Simple Python scripts to test your GoQuorum network.

## Requirements

- Python 3.10+
- Network running from `docker-compose-evote.yml`
- RPC defaults:
  - HTTP: `http://localhost:18545`
  - HTTPS (proxy): `https://localhost:18443` (self-signed)

## Setup (Windows PowerShell)

```powershell
cd C:\laragon\www\quorum-network\quorum-examples\py
py -3 -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

## 1) Connectivity test

Print client version, chain id, and latest block:

```powershell
python .\rpc_test.py --rpc http://localhost:18545
```

Optional: send a 0-wei tx from an unlocked account on the node (the compose unlocks account index 0 on each node). Provide the sender address:

```powershell
# Example: from the penyelenggara node address (replace with your 0x...)
$env:FROM_ADDRESS = "0xYourFromAddress"
python .\rpc_test.py --rpc http://localhost:18545 --send --from $env:FROM_ADDRESS
```

## 2) Read EVote contract count

If you have a deployed contract and an ABI file path:

```powershell
$env:CONTRACT_ADDRESS = "0xDeployedContractAddress"
$env:CONTRACT_ABI_PATH = "C:\\laragon\\www\\quorum-network\\quorum-examples\\evote-deploy\\evote_abi.json"
python .\evote_read.py my-election-id --rpc http://localhost:18545
```

Notes:
- ABI may be either a pure ABI array or a full artifact with an `abi` key; the script handles both.
- For HTTPS proxy, pass `--rpc https://localhost:18443` and accept the self-signed cert (or use `REQUESTS_CA_BUNDLE`).
