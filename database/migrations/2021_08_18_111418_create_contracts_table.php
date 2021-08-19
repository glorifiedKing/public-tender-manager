<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->integer('idcontrato')->unique();
            $table->text('nAnuncio')->nullable();
            $table->text('tipoContrato');
            $table->text('tipoprocedimento');
            $table->text('objectoContrato');
            $table->text('adjudicantes')->nullable();
            $table->text('adjudicatarios');
            $table->date('dataPublicacao');
            $table->date('dataCelebracaoContrato')->index();
            $table->decimal('precoContratual',13,2)->index();
            $table->text('cpv');
            $table->integer('prazoExecucao');
            $table->text('localExecucao');
            $table->text('fundamentacao');
            $table->boolean('read')->default(false);
            $table->foreignId('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
