<?php
use Phinx\Seed\AbstractSeed;

/**
 * DataAssetsSecurityService seed.
 */
class DataAssetsSecurityServiceSeed extends AbstractSeed
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
                'data_asset_id' => '1',
                'security_service_id' => '18',
            ],
            [
                'id' => '2',
                'data_asset_id' => '1',
                'security_service_id' => '16',
            ],
        ];

        $table = $this->table('data_assets_security_services');
        $table->insert($data)->save();
    }
}
