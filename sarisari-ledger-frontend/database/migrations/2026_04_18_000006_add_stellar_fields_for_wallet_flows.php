<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('stellar_public_key', 56)->nullable()->after('code');
            $table->index('stellar_public_key');
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->string('wallet_tx_hash', 80)->nullable()->after('balance_after');
            $table->unique('wallet_tx_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropUnique(['wallet_tx_hash']);
            $table->dropColumn('wallet_tx_hash');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['stellar_public_key']);
            $table->dropColumn('stellar_public_key');
        });
    }
};
