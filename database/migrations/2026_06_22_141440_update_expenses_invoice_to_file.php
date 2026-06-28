<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_date']);
            $table->string('invoice_file')->nullable()->after('payment_method');
            $table->string('invoice_file_name')->nullable()->after('invoice_file');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['invoice_file', 'invoice_file_name']);
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
        });
    }
};
