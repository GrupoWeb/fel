<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('codigoMoneda');
            $table->string('fechaHoraEmision');
            $table->string('tipo');
            $table->string('afiliacionIVA');
            $table->string('codigoEstablecimiento');
            $table->string('correoEmisor');
            $table->string('nitEmisor');
            $table->string('nombreComercial');
            $table->string('nombreEmisor');
            $table->string('direccionEmisor');
            $table->string('correoReceptor');
            $table->string('idReceptor');
            $table->string('nombreReceptor');
            $table->string('direccionReceptor');
            $table->string('cantidad');
            $table->string('descripcion');
            $table->string('precio');
            $table->string('descuento');
            $table->string('total');
            $table->string('granTotal');
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
        Schema::dropIfExists('facturas');
    }
}
