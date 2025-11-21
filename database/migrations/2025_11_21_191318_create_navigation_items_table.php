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
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // Display text in menu
            $table->enum('type', ['category', 'page', 'custom', 'divider']); // Link type
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('page_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('custom_url')->nullable(); // For custom/external links
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->onDelete('cascade'); // Hierarchy
            $table->integer('order')->default(0)->index(); // For ordering/drag-drop
            $table->boolean('is_active')->default(true);
            $table->boolean('open_in_new_tab')->default(false); // For external links
            $table->string('css_classes')->nullable(); // Custom styling
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
