<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StoreStateEnum;
use App\Enums\StoreTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256);
            $table->decimal('lat', 10, 7);
            $table->decimal('long', 10, 7);

            /**
             * The contents of these columns are controlled using PHP enums so information can be
             * added without needing a migration - these can also be used for validation on requests.
             *
             * I have defaulted stores to CLOSED to prevent accidents and created an UNKNOWN type as default
             * for the type. Obviously we should never end up with an UNKNOWN store so this gives us a handy
             * way to report a problem later if we find any?
             */
            $table->string('state', 128)->default(StoreStateEnum::CLOSED->value);
            $table->string('type', 128)->default(StoreTypeEnum::UNKNOWN->value);
            $table->integer('max_delivery_distance');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
