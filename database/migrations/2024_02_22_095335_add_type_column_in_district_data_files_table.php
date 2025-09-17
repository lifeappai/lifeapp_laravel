<?php

use App\Enums\FileTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeColumnInDistrictDataFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('district_data_files', function (Blueprint $table) {
            $table->tinyInteger("file_type")->after('path')->default(FileTypeEnum::DISTRICT)->comment("1 => District, 2 => Student");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('district_data_files', function (Blueprint $table) {
            $table->dropColumn("file_type");
        });
    }
}
