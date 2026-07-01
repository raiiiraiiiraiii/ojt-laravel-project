<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            if (! Schema::hasColumn('todos', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });

        Schema::table('todos', function (Blueprint $table) {
            if (! Schema::hasColumn('todos', 'status')) {
                $table->string('status', 32)
                    ->default('todo')
                    ->after('deadline')
                    ->index();
            }
        });

        if (
            Schema::hasColumn('todos', 'is_completed') &&
            Schema::hasColumn('todos', 'status')
        ) {
            DB::table('todos')
                ->where('is_completed', true)
                ->update(['status' => 'done']);
        }

        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasColumn('todos', 'is_completed')) {
                $table->dropColumn('is_completed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            if (! Schema::hasColumn('todos', 'is_completed')) {
                $table->boolean('is_completed')
                    ->default(false)
                    ->after('deadline');
            }
        });

        if (
            Schema::hasColumn('todos', 'status') &&
            Schema::hasColumn('todos', 'is_completed')
        ) {
            DB::table('todos')
                ->where('status', 'done')
                ->update(['is_completed' => true]);
        }

        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasColumn('todos', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasColumn('todos', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};