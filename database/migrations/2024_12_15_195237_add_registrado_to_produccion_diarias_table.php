<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produccion_diarias', function (Blueprint $table) {
            $table->boolean('registrado')->default(false)->after('observaciones');
        });
    }

    public function down()
    {
        Schema::table('produccion_diarias', function (Blueprint $table) {
            $table->dropColumn('registrado');
        });
    }
};

