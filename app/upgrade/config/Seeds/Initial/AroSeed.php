<?php
use Phinx\Seed\AbstractSeed;

/**
 * Aro seed.
 */
class AroSeed extends AbstractSeed
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
                'parent_id' => NULL,
                'model' => 'Group',
                'foreign_key' => '10',
                'alias' => NULL,
                'lft' => '1',
                'rght' => '16',
            ],
            [
                'id' => '2',
                'parent_id' => '11',
                'model' => 'User',
                'foreign_key' => '1',
                'alias' => NULL,
                'lft' => '2',
                'rght' => '3',
            ],
            [
                'id' => '3',
                'parent_id' => NULL,
                'model' => 'Group',
                'foreign_key' => '11',
                'alias' => NULL,
                'lft' => '17',
                'rght' => '18',
            ],
            [
                'id' => '4',
                'parent_id' => NULL,
                'model' => 'Group',
                'foreign_key' => '12',
                'alias' => NULL,
                'lft' => '19',
                'rght' => '20',
            ],
            [
                'id' => '5',
                'parent_id' => NULL,
                'model' => 'Group',
                'foreign_key' => '13',
                'alias' => NULL,
                'lft' => '21',
                'rght' => '22',
            ],
        ];

        $table = $this->table('aros');
        $table->insert($data)->save();
    }
}
