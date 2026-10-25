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
        Schema::create('servis', function (Blueprint $table) {
            $table->id('id_servis');
            $table->string('no_servis')->unique();
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->restrictOnDelete();
            $table->foreignId('id_petugas')->constrained('users', 'id_user')->restrictOnDelete();
            $table->foreignId('id_teknisi')->nullable()->constrained('users', 'id_user')->restrictOnDelete();
            $table->string('jenis_perangkat');
            $table->string('merk_tipe');
            $table->text('keluhan');
            $table->text('diagnosis')->nullable();
            $table->text('tindakan')->nullable();
            $table->enum('status', ['diterima', 'diperiksa', 'diperbaiki', 'selesai', 'diambil'])->default('diterima')->index();
            $table->date('tgl_masuk')->index();
            $table->date('tgl_selesai')->nullable();
            $table->date('tgl_diambil')->nullable();
            $table->integer('biaya_jasa')->default(0);
            $table->integer('total_biaya')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servis');
    }
};
