<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config()->string('request-chronicle.table_name'), function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('method');
            $table->string('url');
            $table->jsonb('headers');
            $table->jsonb('payload');
            $table->text('raw');
            $table->jsonb('ips');
            $table->nullableMorphs('model');
            $table->timestamp('created_at');
        });
    }
};
