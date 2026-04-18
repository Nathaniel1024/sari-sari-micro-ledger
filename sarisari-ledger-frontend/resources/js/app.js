import {
    getAddress,
    isAllowed,
    setAllowed,
    signTransaction,
} from "@stellar/freighter-api";
import {
    Account,
    Asset,
    BASE_FEE,
    Operation,
    Transaction,
    TransactionBuilder,
} from "@stellar/stellar-sdk";

const stellarConfig = window.StellarConfig ?? {};

const uiAlert = (message) => {
    window.alert(message);
};

const isStellarAccount = (value) => /^G[A-Z2-7]{55}$/.test(String(value ?? ""));

const toStellarAmount = (phpValue) => {
    const numeric = Number(phpValue);
    if (!Number.isFinite(numeric) || numeric <= 0) {
        throw new Error("Amount must be greater than zero.");
    }

    return numeric.toFixed(7);
};

const ensureFreighterAccess = async () => {
    const allowed = await isAllowed();
    if (!allowed.isAllowed) {
        const permission = await setAllowed();
        if (permission.error) {
            throw new Error(permission.error);
        }
    }

    const addressRes = await getAddress();
    if (addressRes.error || !addressRes.address) {
        throw new Error(addressRes.error || "Unable to get Freighter address.");
    }

    return addressRes.address;
};

const loadAccount = async (publicKey) => {
    const res = await fetch(`${stellarConfig.horizonUrl}/accounts/${publicKey}`);
    if (!res.ok) {
        throw new Error("Unable to load Stellar account from Horizon.");
    }
    return res.json();
};

const submitSignedTransaction = async (signedXdr) => {
    const tx = new Transaction(signedXdr, stellarConfig.networkPassphrase);
    const txEnvelope = tx.toEnvelope().toXDR("base64");

    const res = await fetch(`${stellarConfig.horizonUrl}/transactions`, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
            tx: txEnvelope,
        }),
    });

    const payload = await res.json();
    if (!res.ok) {
        const detail =
            payload?.extras?.result_codes?.operations?.[0] ||
            payload?.detail ||
            "Transaction submission failed.";
        throw new Error(detail);
    }

    return payload.hash;
};

const sendPaymentWithFreighter = async ({ destination, amount }) => {
    if (!stellarConfig.horizonUrl || !stellarConfig.networkPassphrase) {
        throw new Error("Stellar configuration is missing.");
    }

    if (!destination) {
        throw new Error("Destination wallet is not set.");
    }

    const sourceAddress = await ensureFreighterAccess();
    const sourceAccount = await loadAccount(sourceAddress);

    const account = new Account(sourceAddress, sourceAccount.sequence);
    const tx = new TransactionBuilder(account, {
        fee: String(BASE_FEE),
        networkPassphrase: stellarConfig.networkPassphrase,
    })
        .addOperation(
            Operation.payment({
                destination,
                asset: Asset.native(),
                amount: toStellarAmount(amount),
            }),
        )
        .setTimeout(180)
        .build();

    const signed = await signTransaction(tx.toXDR(), {
        networkPassphrase: stellarConfig.networkPassphrase,
        address: sourceAddress,
    });

    if (signed.error || !signed.signedTxXdr) {
        throw new Error(signed.error || "Freighter signature failed.");
    }

    const hash = await submitSignedTransaction(signed.signedTxXdr);
    return { hash, sourceAddress };
};

const handleAdminCredit = async (form) => {
    const destination = stellarConfig.storePublicKey;
    const amount = form.querySelector('input[name="amount"]')?.value;
    const hashField = form.querySelector('input[name="wallet_tx_hash"]');

    if (!destination) {
        throw new Error("Store Stellar public key is not configured.");
    }
    if (!isStellarAccount(destination)) {
        throw new Error(
            "Store destination must be a Stellar account public key (G...). Contract IDs (C...) cannot receive payment operations.",
        );
    }

    const { hash } = await sendPaymentWithFreighter({ destination, amount });
    hashField.value = hash;
};

const handleCustomerPayment = async (form) => {
    const destination = stellarConfig.storePublicKey;
    const amount = form.querySelector('input[name="amount"]')?.value;
    const hashField = form.querySelector('input[name="wallet_tx_hash"]');

    if (!destination) {
        throw new Error("Store Stellar public key is not configured.");
    }
    if (!isStellarAccount(destination)) {
        throw new Error(
            "Store destination must be a Stellar account public key (G...). Contract IDs (C...) cannot receive payment operations.",
        );
    }

    const { hash } = await sendPaymentWithFreighter({ destination, amount });
    hashField.value = hash;
};

const handleAdminPayment = async (form) => {
    const destination = stellarConfig.storePublicKey;
    const amount = form.querySelector('input[name="payment"]')?.value;
    const hashField = form.querySelector('input[name="wallet_tx_hash"]');

    if (!destination) {
        throw new Error("Store Stellar public key is not configured.");
    }
    if (!isStellarAccount(destination)) {
        throw new Error(
            "Store destination must be a Stellar account public key (G...). Contract IDs (C...) cannot receive payment operations.",
        );
    }

    const { hash } = await sendPaymentWithFreighter({ destination, amount });
    hashField.value = hash;
};

document.addEventListener("submit", async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const flow = form.dataset.walletFlow;
    if (!flow) {
        return;
    }

    event.preventDefault();

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton?.textContent ?? "";

    try {
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = "Processing wallet transaction...";
        }

        if (flow === "admin-credit") {
            await handleAdminCredit(form);
        } else if (flow === "admin-payment") {
            await handleAdminPayment(form);
        } else if (flow === "customer-payment") {
            await handleCustomerPayment(form);
        }

        form.submit();
    } catch (error) {
        uiAlert(error instanceof Error ? error.message : "Wallet transaction failed.");
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = originalText || "Submit";
        }
    }
});
