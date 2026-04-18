<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# 🏪 Sari-Sari Micro-Ledger Frontend (Laravel)

**Digitizing the Philippine Listahan with Stellar Soroban & Laravel**

---

## 📖 Project Overview

Sari-Sari Micro-Ledger (SSML) is a decentralized credit management system for micro-retailers in the Philippines. It replaces the informal "Listahan" (notebook credit) with a tamper-proof, on-chain ledger using Stellar Soroban smart contracts. This Laravel-based frontend provides a user-friendly interface for store owners and customers to track, add, and pay debts securely.

## ✨ Features

- Transparent debt tracking (on-chain)
- Add and pay credits with Stellar wallet integration
- Customer and admin roles
- Real-time balance and transaction history
- Secure authentication and authorization

## 🛠️ Tech Stack

- **Backend:** Laravel 13 (PHP 8.3)
- **Frontend:** Blade, Livewire 3
- **Blockchain:** Stellar Soroban (via [@stellar/stellar-sdk](https://www.npmjs.com/package/@stellar/stellar-sdk), [@stellar/freighter-api](https://www.npmjs.com/package/@stellar/freighter-api))
- **Styling:** Tailwind CSS
- **Build Tool:** Vite

## 🚀 Getting Started

### Prerequisites

- PHP 8.3+
- Composer
- Node.js 18+
- npm
- MySQL (or SQLite for local dev)

### Installation

```bash
# Clone the repo
git clone <this-repo>
cd sarisari-ledger-frontend

# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Set up database (edit .env as needed)
php artisan migrate

# Install JS dependencies and build assets
npm install
npm run build

# Start the local server
php artisan serve
```

### Dev Mode

```bash
npm run dev
# In another terminal
php artisan serve
```

## 🏗️ Project Structure

```
sarisari-ledger-frontend/
├── app/
│   ├── Http/Controllers/        # Auth, Ledger logic
│   ├── Http/Middleware/         # Role-based access
│   ├── Livewire/Admin/          # Admin Livewire components
│   ├── Livewire/User/           # User Livewire components
│   ├── Models/                  # Eloquent models (Customer, LedgerEntry, User)
│   └── Providers/               # AppServiceProvider
├── resources/views/             # Blade templates
├── resources/js/                # JS (Stellar SDK integration)
├── resources/css/               # Tailwind styles
├── routes/web.php               # Web routes
├── database/migrations/         # DB schema
├── tests/                       # Feature & unit tests
├── public/                      # Public assets & entrypoint
├── composer.json                # PHP dependencies
├── package.json                 # JS dependencies
└── vite.config.js               # Vite config
```

## 🌐 Stellar & Smart Contract

- [Soroban Contract Source](../../contract/)
- [Deployed Contract (Testnet)](https://stellar.expert/explorer/testnet/contract/CBJEB7D4NS2QSQ7SOWDTOAY6MA5JSWXHL6DURPGYB26HVXSYB6E3IGT6)
- [Deployment Logs](./docs/deployed.md)

## 📚 Documentation

- [Project Overview](./docs/AI_CONTEXT.md)
- [Demo & Pitch](./docs/peach.md)

## 🤝 Contributing

PRs and issues welcome! See [docs/AI_CONTEXT.md](./docs/AI_CONTEXT.md) for technical context.

## 🛡️ License

MIT. See [LICENSE](../LICENSE).
