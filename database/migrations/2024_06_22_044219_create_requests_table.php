<?php

use App\Models\Bag;
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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bag::class)->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('method');
            $table->string('url');
            $table->json('headers');
            $table->json('payload');
            $table->text('raw');
            $table->json('ips');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
