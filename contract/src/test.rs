#![cfg(test)]
use super::*;
use soroban_sdk::{testutils::Address as _, Address, Env};

#[test]
fn test_happy_path_credit_flow() {
    let env = Env::default();
    let contract_id = env.register(SariSariLedger, ());
    let client = SariSariLedgerClient::new(&env, &contract_id);
    let customer = Address::generate(&env);

    env.mock_all_auths();

    // 1. Record 100 PHP debt
    client.add_credit(&customer, &100);
    assert_eq!(client.get_debt(&customer), 100);

    // 2. Pay off 40 PHP
    client.pay_credit(&customer, &40);
    assert_eq!(client.get_debt(&customer), 60);
}

#[test]
#[should_panic(expected = "Payment exceeds current debt")]
fn test_overpayment_failure() {
    let env = Env::default();
    let contract_id = env.register(SariSariLedger, ());
    let client = SariSariLedgerClient::new(&env, &contract_id);
    let customer = Address::generate(&env);

    env.mock_all_auths();

    client.add_credit(&customer, &50);
    client.pay_credit(&customer, &60); // Should fail
}

#[test]
fn test_storage_persistence() {
    let env = Env::default();
    let contract_id = env.register(SariSariLedger, ());
    let client = SariSariLedgerClient::new(&env, &contract_id);
    let customer = Address::generate(&env);

    env.mock_all_auths();

    client.add_credit(&customer, &500);
    // Verify state matches exactly
    assert_eq!(env.as_contract(&contract_id, || {
        env.storage().persistent().get::<DataKey, i128>(&DataKey::Credit(customer)).unwrap()
    }), 500);
}

#[test]
fn test_multiple_customers_isolation() {
    let env = Env::default();
    let contract_id = env.register(SariSariLedger, ());
    let client = SariSariLedgerClient::new(&env, &contract_id);
    
    let alice = Address::generate(&env);
    let bob = Address::generate(&env);

    env.mock_all_auths();

    client.add_credit(&alice, &100);
    client.add_credit(&bob, &200);

    assert_eq!(client.get_debt(&alice), 100);
    assert_eq!(client.get_debt(&bob), 200);
}

#[test]
fn test_zero_initial_debt() {
    let env = Env::default();
    let contract_id = env.register(SariSariLedger, ());
    let client = SariSariLedgerClient::new(&env, &contract_id);
    let stranger = Address::generate(&env);

    assert_eq!(client.get_debt(&stranger), 0);
}

#[test]
#[should_panic(expected = "Credit amount must be greater than zero")]
fn test_add_credit_zero_fails() {
    let env = Env::default();
    let contract_id = env.register(SariSariLedger, ());
    let client = SariSariLedgerClient::new(&env, &contract_id);
    let customer = Address::generate(&env);

    env.mock_all_auths();
    client.add_credit(&customer, &0);
}

#[test]
#[should_panic(expected = "Payment amount must be greater than zero")]
fn test_pay_credit_zero_fails() {
    let env = Env::default();
    let contract_id = env.register(SariSariLedger, ());
    let client = SariSariLedgerClient::new(&env, &contract_id);
    let customer = Address::generate(&env);

    env.mock_all_auths();
    client.add_credit(&customer, &50);
    client.pay_credit(&customer, &0);
}