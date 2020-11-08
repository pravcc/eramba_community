<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetLabel seed.
 */
class AssetLabelSeed extends AbstractSeed
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
                'name' => 'Confidentiality',
                'description' => '',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:47:18',
                'modified' => '2017-04-10 15:47:18',
            ],
            [
                'id' => '2',
                'name' => 'Internal',
                'description' => '',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:47:26',
                'modified' => '2017-04-10 15:47:26',
            ],
        ];

        $table = $this->table('asset_labels');
        $table->insert($data)->save();
    }
}
