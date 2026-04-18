#![no_std]
use soroban_sdk::{contract, contractimpl, contracttype, Address, Env};

#[contracttype]
#[derive(Clone)]
pub enum DataKey {
    Credit(Address), // Stores the credit balance of a specific customer
}

#[contract]
pub struct SariSariLedger;

#[contractimpl]
impl SariSariLedger {
    /// Records a new credit (utang) for a customer.
    /// Only the store owner (contract deployer) should ideally call this in a full version.
    pub fn add_credit(env: Env, customer: Address, amount: i128) {
        customer.require_auth();
        
        if amount <= 0 {
            panic!("Credit amount must be greater than zero");
        }

        let key = DataKey::Credit(customer.clone());
        let current_balance: i128 = env.storage().persistent().get(&key).unwrap_or(0);
        
        // Add the new amount to the existing debt
        let new_balance = current_balance.checked_add(amount).expect("Integer overflow");
        env.storage().persistent().set(&key, &new_balance);
    }

    /// Pays off a portion or all of a customer's credit.
    pub fn pay_credit(env: Env, customer: Address, payment: i128) {
        customer.require_auth();
        
        if payment <= 0 {
            panic!("Payment amount must be greater than zero");
        }

        let key = DataKey::Credit(customer.clone());
        let current_balance: i128 = env.storage().persistent().get(&key).unwrap_or(0);

        if payment > current_balance {
            panic!("Payment exceeds current debt");
        }

        let new_balance = current_balance - payment;
        env.storage().persistent().set(&key, &new_balance);
    }

    /// View-only function to return the current balance of a customer.
    pub fn get_debt(env: Env, customer: Address) -> i128 {
        let key = DataKey::Credit(customer);
        env.storage().persistent().get(&key).unwrap_or(0)
    }
}

#[cfg(test)]
mod test;