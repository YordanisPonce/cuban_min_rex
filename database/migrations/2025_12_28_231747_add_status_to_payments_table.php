<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('user_id'); // pending|succeeded|failed
            $table->text('error_message')->nullable()->after('paypal_response');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'error_message', 'paid_at']);
        });
    }
};
