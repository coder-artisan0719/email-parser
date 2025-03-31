<?php

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
        Schema::create('successful_emails', function (Blueprint $table) {
            $table->id();
            $table->mediumInteger('affiliate_id');
            $table->text('envelope');
            $table->string('from', 255);
            $table->text('subject');
            $table->string('dkim', 255)->nullable();
            $table->string('SPF', 255)->nullable();
            $table->float('spam_score')->nullable();
            $table->longText('email');
            $table->longText('raw_text');
            $table->string('sender_ip', 50)->nullable();
            $table->text('to');
            $table->integer('timestamp');
            $table->softDeletes();
            $table->index('affiliate_id', 'affiliate_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('successful_emails');
    }
};
