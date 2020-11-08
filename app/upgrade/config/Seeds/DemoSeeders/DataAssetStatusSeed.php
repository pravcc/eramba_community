<?php
use Phinx\Seed\AbstractSeed;

/**
 * DataAssetStatus seed.
 */
class DataAssetStatusSeed extends AbstractSeed
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
                'name' => 'Created',
            ],
            [
                'id' => '2',
                'name' => 'Modified',
            ],
            [
                'id' => '3',
                'name' => 'Stored',
            ],
            [
                'id' => '4',
                'name' => 'Transit',
            ],
            [
                'id' => '5',
                'name' => 'Deleted',
            ],
            [
                'id' => '6',
                'name' => 'Tainted / Broken',
            ],
            [
                'id' => '7',
                'name' => 'Unnecessary',
            ],
        ];

        $table = $this->table('data_asset_statuses');
        $table->insert($data)->save();
    }
}
