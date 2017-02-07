<?php

use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;

/**
 * Class HistoryTypeTableSeeder
 */
class HistoryTypeTableSeeder extends Seeder
{

    use \database\DisablesForeignKeys;


  /**
   * Run the database seed.
   *
   * @return void
   */
    public function run()
    {

        $this->disableForeignKeys();

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::table('history_types')->truncate();
        } elseif (DB::connection()->getDriverName() == 'sqlite') {
            DB::statement('DELETE FROM history_types');
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE history_types CASCADE');
        }

        $types = [
        [
            'id'         => 1,
            'name'       => 'User',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
        [
            'id'         => 2,
            'name'       => 'Role',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
        ];

        DB::table('history_types')->insert($types);

        $this->enableForeignKeys();
    }
}
