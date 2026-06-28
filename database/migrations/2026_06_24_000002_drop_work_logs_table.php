<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('work_logs');
    }

    public function down(): void
    {
        // Restore via original migration if needed
    }
};
