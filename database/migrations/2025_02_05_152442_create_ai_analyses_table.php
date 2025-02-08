<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->foreignId('request_id')->unique()->constrained(config()->string('request-chronicle.table_name'));
            $table->jsonb('analysis_result');
            $table->timestamp('created_at');
        });
    }
};
