<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegFileImportHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reg_file_import_history', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->text('map')->nullable()->comment('stores the serialized column map array');
			$table->integer('user_id')->unsigned()->nullable()->index();
			$table->integer('vocabulary_id')->unsigned()->nullable()->index();
			$table->integer('schema_id')->unsigned()->nullable()->index();
			$table->string('file_name')->nullable();
			$table->string('source_file_name')->nullable();
			$table->string('file_type', 30)->nullable();
			$table->integer('batch_id')->unsigned()->nullable()->index();
			$table->text('results')->nullable()->comment('stores the serialized results of the import');
			$table->integer('total_processed_count')->nullable();
			$table->integer('error_count')->nullable();
			$table->integer('success_count')->nullable();
			$table->integer('token')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reg_file_import_history');
	}

}
