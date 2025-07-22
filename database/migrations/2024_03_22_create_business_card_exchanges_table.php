<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('business_card_exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sender_card_id')->constrained('business_cards')->onDelete('cascade');
            $table->foreignId('receiver_card_id')->constrained('business_cards')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained()->onDelete('set null');
            $table->string('exchange_method')->default('digital'); // digital, qr_code, nfc, etc.
            $table->json('metadata')->nullable(); // Stockage d'informations supplémentaires
            $table->timestamps();

            // Index pour améliorer les performances des requêtes
            $table->index('created_at');
            $table->index(['sender_id', 'receiver_id']);
            $table->index('event_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_card_exchanges');
    }
}; 