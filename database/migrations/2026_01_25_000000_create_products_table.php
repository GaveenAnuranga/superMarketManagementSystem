<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //UP methord - php artisan migrate
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->timestamps();
        });  
    }

    
    //down methord - php artisan migrate:rollback
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
