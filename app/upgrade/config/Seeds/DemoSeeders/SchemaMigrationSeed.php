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
                'created' => '2016-12-12 17:05:35',
            ],
            [
                'id' => '6',
                'class' => 'E101002',
                'type' => 'app',
                'created' => '2016-12-12 17:08:04',
            ],
            [
                'id' => '7',
                'class' => 'E101003',
                'type' => 'app',
                'created' => '2016-12-12 17:09:50',
            ],
            [
                'id' => '8',
                'class' => 'E101004',
                'type' => 'app',
                'created' => '2016-12-12 17:10:14',
            ],
            [
                'id' => '9',
                'class' => 'E101005',
                'type' => 'app',
                'created' => '2016-12-12 17:12:06',
            ],
            [
                'id' => '10',
                'class' => 'E101006',
                'type' => 'app',
                'created' => '2016-12-12 17:12:38',
            ],
            [
                'id' => '11',
                'class' => 'E101007',
                'type' => 'app',
                'created' => '2016-12-12 17:13:45',
            ],
            [
                'id' => '12',
                'class' => 'E101008',
                'type' => 'app',
                'created' => '2016-12-12 17:14:27',
            ],
            [
                'id' => '13',
                'class' => 'E101009',
                'type' => 'app',
                'created' => '2016-12-12 17:15:06',
            ],
            [
                'id' => '14',
                'class' => 'E101010',
                'type' => 'app',
                'created' => '2017-03-11 08:22:24',
            ],
            [
                'id' => '15',
                'class' => 'E101011',
                'type' => 'app',
                'created' => '2017-03-11 08:30:13',
            ],
            [
                'id' => '16',
                'class' => 'E101012',
                'type' => 'app',
                'created' => '2017-03-11 08:35:52',
            ],
            [
                'id' => '17',
                'class' => 'E101013',
                'type' => 'app',
                'created' => '2017-03-11 08:56:41',
            ],
            [
                'id' => '18',
                'class' => 'E101014',
                'type' => 'app',
                'created' => '2017-03-11 09:05:31',
            ],
            [
                'id' => '19',
                'class' => 'E101015',
                'type' => 'app',
                'created' => '2017-03-11 17:57:42',
            ],
            [
                'id' => '20',
                'class' => 'E101016',
                'type' => 'app',
                'created' => '2017-04-03 09:08:47',
            ],
        ];

        $table = $this->table('schema_migrations');
        $table->insert($data)->save();
    }
}
