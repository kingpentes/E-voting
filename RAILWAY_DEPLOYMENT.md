# Railway Deployment Guide

## Environment Variables yang Diperlukan

Pastikan semua environment variables berikut sudah diset di Railway Dashboard:

### Application
```
APP_NAME=E-Voting
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:SpmuedSDP4JJD9pHr4cXGfBEds4wITSwksMMDA4u/Fg=
APP_URL=https://smart-voting.up.railway.app
```

### Database
```
DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=YSWhfdeEKLiVqvtXvBzDYnKFTucYLXlz
```

### Blockchain (PENTING!)
```
BLOCKCHAIN_RPC=http://202.10.34.252:18545
BLOCKCHAIN_FROM=0xed9d02e382b34818e88b88a309c7fe71e65f419d
BLOCKCHAIN_GAS=0x7a1200

# Path untuk deployment scripts
CONTRACT_DEPLOY_PATH=/app/scripts

# Path untuk ABI file (akan dibuat saat deploy contract)
CONTRACT_ABI_PATH=/app/storage/contract/evote_abi.json

# Contract address (akan diisi setelah deploy per-election)
CONTRACT_ADDRESS=0x1932c48b2bF8102Ba33B4A6B545C32236e342f34

# Encryption key untuk vote
VOTE_ENC_KEY=base64:9n9kETCjkhMCE+LsWXiybbp+beGcyB+ByOFZzOe8N68=
```

### Cache & Session
```
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
```

### Logging
```
LOG_CHANNEL=stderr
LOG_LEVEL=error
LOG_STACK=single
```

### Google OAuth
```
GOOGLE_CLIENT_ID=236667411997-ahf7pqcdck56fep2q39aoq1cmr9n3h7e.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI=https://smart-voting.up.railway.app/auth/google/callback
```

### Gmail API Credentials (untuk OTP)
```
GMAIL_TOKEN_JSON={"access_token":"...","refresh_token":"..."}
GOOGLE_APPLICATION_CREDENTIALS={"web":{"client_id":"...","client_secret":"..."}}
```

### Payment Gateway (Midtrans)
```
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SERVER_KEY=your-midtrans-server-key
MIDTRANS_CLIENT_KEY=your-midtrans-client-key
MIDTRANS_MERCHANT_ID=your-merchant-id
```

### Mail
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_FROM_ADDRESS=noreply@evoting.local
MAIL_FROM_NAME="E-Voting System"
```

## Build Process

Railway akan otomatis:
1. Install PHP dependencies dengan Composer
2. Install Node.js dependencies untuk frontend
3. Install Node.js dependencies untuk blockchain scripts (`scripts/node_modules`)
4. Build assets dengan Vite
5. Cache Laravel config, routes, dan views
6. Run migrations
7. Start PHP server

## Troubleshooting

### Error: "Deploy folder not found: CONTRACT_DEPLOY_PATH not set"
**Solusi:** Tambahkan `CONTRACT_DEPLOY_PATH=/app/scripts` di Railway environment variables

### Error: "Deploy failed" tanpa detail
**Kemungkinan:**
1. Node.js dependencies belum terinstall di folder scripts
2. File contract Solidity tidak ada
3. Blockchain RPC tidak bisa diakses dari Railway

**Solusi:**
- Pastikan folder `scripts/contracts/EvoteEncrypted.sol` ada di repository
- Pastikan `nixpacks.toml` sudah di-commit
- Cek logs Railway untuk detail error: `railway logs`
- Pastikan blockchain RPC (`http://202.10.34.252:18545`) bisa diakses dari internet

### Error: "storeVote.js failed"
**Solusi:**
- Pastikan blockchain node bisa diakses dari Railway
- Cek apakah account `BLOCKCHAIN_FROM` sudah di-unlock di blockchain node
- Pastikan gas limit cukup (`BLOCKCHAIN_GAS=0x7a1200`)

## Deploy Command

```bash
# Commit perubahan
git add .
git commit -m "Add blockchain contract and Railway config"
git push origin main

# Railway akan otomatis deploy
```

## Testing Setelah Deploy

1. Login ke aplikasi
2. Buat pemilu baru
3. Klik tombol "Deploy Smart Contract" di halaman manage pemilu
4. Jika berhasil, contract address akan tersimpan dan ditampilkan
5. Coba voting untuk test deployment
