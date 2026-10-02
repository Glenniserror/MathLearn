<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('password_automatically_set')->default(false)->after('password');
        });

        // Google sign-up gave students and teachers a random password they
        // never saw. Accounts that registered through the form and only
        // linked Google later (a registration log without "(Google)") keep
        // their own password, and admins are never created through Google.
        DB::table('users')
            ->whereNotNull('google_id')
            ->where('role', '!=', 'admin')
            ->whereNotExists(fn (Builder $query) => $query->selectRaw('1')
                ->from('activity_logs')
                ->whereColumn('activity_logs.user_id', 'users.id')
                ->where('activity_logs.type', 'registration')
                ->where('activity_logs.title', 'not like', '%(Google)'))
            ->update(['password_automatically_set' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_automatically_set');
        });
    }
};
