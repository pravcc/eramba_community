<?php
use Phinx\Seed\AbstractSeed;

/**
 * SchemaMigration seed.
 */
class SchemaMigrationSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'class' => 'InitMigrations',
                'type' => 'Migrations',
                'created' => '2016-01-17 20:45:25',
            ],
            [
                'id' => '2',
                'class' => 'ConvertVersionToClassNames',
                'type' => 'Migrations',
                'created' => '2016-01-17 20:45:25',
            ],
            [
                'id' => '3',
                'class' => 'IncreaseClassNameLength',
                'type' => 'Migrations',
                'created' => '2016-01-17 20:45:25',
            ],
            [
                'id' => '4',
                'class' => 'E101000',
                'type' => 'app',
                'created' => '2016-01-17 20:47:16',
            ],
            [
                'id' => '5',
                'class' => 'E101001',
                'type' => 'app',
                'created' => '2016-11-18 14:34:44',
            ],
            [
                'id' => '6',
                'class' => 'E101002',
                'type' => 'app',
                'created' => '2016-11-18 14:38:23',
            ],
            [
                'id' => '7',
                'class' => 'E101003',
                'type' => 'app',
                'created' => '2016-11-18 14:39:17',
            ],
            [
                'id' => '8',
                'class' => 'E101004',
                'type' => 'app',
                'created' => '2016-11-18 14:39:23',
            ],
            [
                'id' => '9',
                'class' => 'E101005',
                'type' => 'app',
                'created' => '2016-11-18 14:40:22',
            ],
            [
                'id' => '10',
                'class' => 'E101006',
                'type' => 'app',
                'created' => '2016-11-18 14:40:47',
            ],
            [
                'id' => '11',
                'class' => 'E101007',
                'type' => 'app',
                'created' => '2016-11-18 14:42:46',
            ],
            [
                'id' => '12',
                'class' => 'E101008',
                'type' => 'app',
                'created' => '2016-11-18 14:47:11',
            ],
            [
                'id' => '13',
                'class' => 'E101009',
                'type' => 'app',
                'created' => '2016-11-18 14:48:32',
            ],
            [
                'id' => '14',
                'class' => 'E101010',
                'type' => 'app',
                'created' => '2017-02-22 21:32:29',
            ],
            [
                'id' => '15',
                'class' => 'E101011',
                'type' => 'app',
                'created' => '2017-02-22 21:32:35',
            ],
            [
                'id' => '16',
                'class' => 'E101012',
                'type' => 'app',
                'created' => '2017-02-22 21:32:37',
            ],
            [
                'id' => '17',
                'class' => 'E101013',
                'type' => 'app',
                'created' => '2017-02-22 21:32:39',
            ],
            [
                'id' => '18',
                'class' => 'E101014',
                'type' => 'app',
                'created' => '2017-02-22 21:32:39',
            ],
            [
                'id' => '19',
                'class' => 'E101015',
                'type' => 'app',
                'created' => '2017-02-22 21:32:40',
            ],
            [
                'id' => '20',
                'class' => 'E101016',
                'type' => 'app',
                'created' => '2017-02-22 21:32:40',
            ],
        ];

        $table = $this->table('schema_migrations');
        $table->insert($data)->save();
    }
}
