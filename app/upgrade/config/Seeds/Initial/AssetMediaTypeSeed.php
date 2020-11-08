<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetMediaType seed.
 */
class AssetMediaTypeSeed extends AbstractSeed
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
                'name' => 'Data Asset',
                'editable' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '2',
                'name' => 'Facilities',
                'editable' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '3',
                'name' => 'People',
                'editable' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '4',
                'name' => 'Hardware',
                'editable' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '5',
                'name' => 'Software',
                'editable' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '6',
                'name' => 'IT Service',
                'editable' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '7',
                'name' => 'Network',
                'editable' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '8',
                'name' => 'Financial',
                'editable' => '0',
                'created' => NULL,
                'modified' => NULL,
            ],
        ];

        $table = $this->table('asset_media_types');
        $table->insert($data)->save();
    }
}
