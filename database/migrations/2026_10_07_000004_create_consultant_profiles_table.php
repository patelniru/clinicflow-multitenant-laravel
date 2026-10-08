<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('consultant_profiles',function(Blueprint $t){$t->id();$t->foreignId('company_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();$t->string('specialty')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();}); } public function down(): void {Schema::dropIfExists('consultant_profiles');} };
