<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // key module: brands, models, colors, warehouses, suppliers, customers, vehicles...
            $table->string('module_key', 50);

            // Quyền CRUD
            $table->boolean('can_create')->default(false);
            $table->boolean('can_read')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'module_key']); // 1 user + 1 module = 1 dòng
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_permissions');
    }
};
