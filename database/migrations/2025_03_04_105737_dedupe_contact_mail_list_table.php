<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dedupe existing table
        DB::table('contact_mail_list')
            ->whereNotIn('ROWID', function ($query) {
                $query->selectRaw('min(ROWID)')
                    ->from('contact_mail_list')
                    ->groupBy('contact_id', 'mail_list_id');
            })->delete();

        // Add unique index constraint
        Schema::table('contact_mail_list', function (Blueprint $table) {
            $table->unique(['mail_list_id', 'contact_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
