<?php
use Phinx\Seed\AbstractSeed;

/**
 * DataAssetsProject seed.
 */
class DataAssetsProjectSeed extends AbstractSeed
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
                'project_id' => '1',
                'data_asset_id' => '1',
                'created' => '2017-04-19 07:53:50',
            ],
        ];

        $table = $this->table('data_assets_projects');
        $table->insert($data)->save();
    }
}
