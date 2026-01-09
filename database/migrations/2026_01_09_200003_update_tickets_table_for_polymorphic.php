<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, create the default ticket types from the enum values
        $refundTypeId = DB::table('ticket_types')->insertGetId([
            'name' => 'Refund Request',
            'slug' => 'refund',
            'description' => 'Request for refund of payment',
            'badge_color' => 'primary',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $complaintTypeId = DB::table('ticket_types')->insertGetId([
            'name' => 'Complaint',
            'slug' => 'complaint',
            'description' => 'General complaint or issue report',
            'badge_color' => 'warning',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add polymorphic columns
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('ticketable_type')->nullable()->after('ticket_number');
            $table->unsignedBigInteger('ticketable_id')->nullable()->after('ticketable_type');
            $table->foreignId('ticket_type_id')->nullable()->after('ticketable_id');
        });

        // Migrate existing data: convert user_id to polymorphic relationship
        DB::table('tickets')->whereNotNull('user_id')->update([
            'ticketable_type' => 'App\\Models\\User',
            'ticketable_id' => DB::raw('user_id'),
        ]);

        // Migrate existing type enum to ticket_type_id
        DB::table('tickets')->where('type', 'refund')->update([
            'ticket_type_id' => $refundTypeId,
        ]);

        DB::table('tickets')->where('type', 'complaint')->update([
            'ticket_type_id' => $complaintTypeId,
        ]);

        // Make the new columns non-nullable after data migration
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('ticketable_type')->nullable(false)->change();
            $table->unsignedBigInteger('ticketable_id')->nullable(false)->change();
            $table->foreignId('ticket_type_id')->nullable(false)->change();
        });

        // Add indexes
        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['ticketable_type', 'ticketable_id']);
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types')->onDelete('restrict');
        });

        // Drop old columns
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropColumn('user_id');
            $table->dropColumn('type');
        });

        // Add new composite index
        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['ticketable_type', 'ticketable_id', 'status']);
        });

        // Assign user types to the default ticket types
        // Refund - only for regular users (patients)
        DB::table('ticket_type_user_types')->insert([
            ['ticket_type_id' => $refundTypeId, 'user_type' => 'user', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Complaint - for all user types
        DB::table('ticket_type_user_types')->insert([
            ['ticket_type_id' => $complaintTypeId, 'user_type' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['ticket_type_id' => $complaintTypeId, 'user_type' => 'clinic_user', 'created_at' => now(), 'updated_at' => now()],
            ['ticket_type_id' => $complaintTypeId, 'user_type' => 'supplier_user', 'created_at' => now(), 'updated_at' => now()],
            ['ticket_type_id' => $complaintTypeId, 'user_type' => 'affiliate_user', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back old columns
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('ticket_number');
            $table->enum('type', ['refund', 'complaint'])->default('complaint')->after('user_id');
        });

        // Migrate data back
        DB::statement("UPDATE tickets SET user_id = ticketable_id WHERE ticketable_type = 'App\\\\Models\\\\User'");

        // Get the type IDs
        $refundType = DB::table('ticket_types')->where('slug', 'refund')->first();
        $complaintType = DB::table('ticket_types')->where('slug', 'complaint')->first();

        if ($refundType) {
            DB::table('tickets')->where('ticket_type_id', $refundType->id)->update(['type' => 'refund']);
        }
        if ($complaintType) {
            DB::table('tickets')->where('ticket_type_id', $complaintType->id)->update(['type' => 'complaint']);
        }

        // Make user_id non-nullable and add FK
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
        });

        // Drop new columns
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropIndex(['ticketable_type', 'ticketable_id']);
            $table->dropIndex(['ticketable_type', 'ticketable_id', 'status']);
            $table->dropColumn(['ticketable_type', 'ticketable_id', 'ticket_type_id']);
        });

        // Delete the default ticket types
        DB::table('ticket_type_user_types')->whereIn('ticket_type_id', function ($query) {
            $query->select('id')->from('ticket_types')->whereIn('slug', ['refund', 'complaint']);
        })->delete();

        DB::table('ticket_types')->whereIn('slug', ['refund', 'complaint'])->delete();
    }
};
