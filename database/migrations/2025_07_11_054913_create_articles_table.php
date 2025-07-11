<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create("articles", function (Blueprint $table) {
            $table->id();
            $table->text("title")->index();
            $table->longText("description");
            $table->longText("url");
            $table->string("lang", 300);
            $table->longText("thumbnail");
            $table->timestamp("time");
            $table->foreignId("article_source_id")->constrained();
            $table->foreignId("article_author_id")->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("articles");
    }
};
