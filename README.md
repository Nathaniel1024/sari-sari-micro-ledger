# Sari-Sari Micro-Ledger (SSML)

A Soroban-based on-chain ledger for sari-sari store credit tracking on Stellar.

## Problem and Solution

Sari-sari stores in the Philippines often track customer debt ("listahan") in paper notebooks. This creates frequent issues such as lost records, disputes, and weak cash-flow visibility.

SSML solves this by moving debt records to a Soroban smart contract, creating a tamper-resistant and auditable source of truth where balances are updated by signed transactions.

## Suggested Timeline for MVP Delivery

- Week 1: Finalize contract interface, set up local dev environment, and write core debt logic.
- Week 2: Add tests for happy path and edge cases, then deploy to Stellar testnet.
- Week 3: Connect basic frontend flows and run end-to-end demo validation.
- Week 4: Polish UX, add documentation, and prepare stakeholder/demo presentation.

## Stellar Features Used

- Soroban contracts: Used for debt state management (`add_credit`, `pay_credit`, `get_debt`).
- XLM transfers: Used on testnet to fund accounts and pay transaction fees.
- Custom tokens: Planned for future loyalty/reward flows (not yet implemented).
- Trustlines: Planned for future custom asset support (not yet implemented).

## Prerequisites

- Rust toolchain (tested locally with `rustc 1.94.1`, `cargo 1.94.1`).
- Soroban CLI (recommended `26.0.0+`; project history shows deployment via `25.2.0`).
- Stellar testnet account and funded keypair for deployment.

## Build Instructions

From the `contract` directory:

```bash
soroban contract build
```

## Test Instructions

From the `contract` directory:

```bash
cargo test
```

## Testnet Deploy

From the `contract` directory:

```bash
soroban contract deploy \
  --wasm target/wasm32-unknown-unknown/release/sarisari_ledger.wasm \
  --source YOUR_IDENTITY \
  --network testnet
```

## Deployed Contract (Testnet)

- Contract ID: `CBJEB7D4NS2QSQ7SOWDTOAY6MA5JSWXHL6DURPGYB26HVXSYB6E3IGT6`
- Stellar Expert (Contract): https://stellar.expert/explorer/testnet/contract/CBJEB7D4NS2QSQ7SOWDTOAY6MA5JSWXHL6DURPGYB26HVXSYB6E3IGT6
- Stellar Lab (Contract): https://lab.stellar.org/r/testnet/contract/CBJEB7D4NS2QSQ7SOWDTOAY6MA5JSWXHL6DURPGYB26HVXSYB6E3IGT6
- Deployment TX (WASM Upload): https://stellar.expert/explorer/testnet/tx/65839215bf2e85cfc0ad08700b2bd5d0c49abb0529dded3709cceeaa1c09c2a4
- Deployment TX (Contract Create): https://stellar.expert/explorer/testnet/tx/71e9e724f66274360896a19055096921734753e0b9c41f35bb09a81845314ae2

## Deployment Screenshot

![Deployed SSML Contract on Stellar Testnet](https://github.com/user-attachments/assets/63b180de-ce7c-4270-8749-0d73705c6d64)

## Sample CLI Invocations (Dummy Certificate Calls)

The following are template examples with dummy arguments, as requested:

```bash
soroban contract invoke \
  --id CBJEB7D4NS2QSQ7SOWDTOAY6MA5JSWXHL6DURPGYB26HVXSYB6E3IGT6 \
  --source alice \
  --network testnet \
  -- \
  register_certificate \
  --student "Juan Dela Cruz" \
  --course "Stellar Bootcamp" \
  --issued_at 1711929600 \
  --certificate_id "CERT-001"

soroban contract invoke \
  --id CBJEB7D4NS2QSQ7SOWDTOAY6MA5JSWXHL6DURPGYB26HVXSYB6E3IGT6 \
  --source bob \
  --network testnet \
  -- \
  verify_certificate \
  --certificate_id "CERT-001"
```

## Current Contract Method Examples (This Repository)

For the current SSML contract methods:

```bash
soroban contract invoke \
  --id CBJEB7D4NS2QSQ7SOWDTOAY6MA5JSWXHL6DURPGYB26HVXSYB6E3IGT6 \
  --source alice \
  --network testnet \
  -- \
  add_credit \
  --customer GBXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX \
  --amount 500

soroban contract invoke \
  --id CBJEB7D4NS2QSQ7SOWDTOAY6MA5JSWXHL6DURPGYB26HVXSYB6E3IGT6 \
  --source alice \
  --network testnet \
  -- \
  get_debt \
  --customer GBXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

## MIT License

This project is licensed under the MIT License. See `contract/cargo.toml` (`license = "MIT"`) for the current license declaration.
