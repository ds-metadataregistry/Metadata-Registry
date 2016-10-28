<?php

use Illuminate\Database\Migrations\Migration;
 use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFulltextIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::statement('CREATE FULLTEXT INDEX `fulltext` ON reg_schema_property_element (`object` DESC);');
      DB::statement('CREATE FULLTEXT INDEX `fulltext` ON reg_concept_property (`object` DESC);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reg_concept_property', function (Blueprint $table) {
            $table->dropIndex('fulltext');
        });
      Schema::table('reg_schema_property_element',
          function (Blueprint $table) {
            $table->dropIndex('fulltext');
          });
    }
}