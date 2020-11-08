<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetsRisk seed.
 */
class AssetsRiskSeed extends AbstractSeed
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
                'id' => '3',
                'asset_id' => '11',
                'risk_id' => '2',
            ],
            [
                'id' => '4',
                'asset_id' => '1',
                'risk_id' => '2',
            ],
            [
                'id' => '5',
                'asset_id' => '3',
                'risk_id' => '2',
            ],
            [
                'id' => '6',
                'asset_id' => '6',
                'risk_id' => '2',
            ],
            [
                'id' => '7',
                'asset_id' => '8',
                'risk_id' => '2',
            ],
            [
                'id' => '8',
                'asset_id' => '10',
                'risk_id' => '1',
            ],
            [
                'id' => '9',
                'asset_id' => '7',
                'risk_id' => '1',
            ],
        ];

        $table = $this->table('assets_risks');
        $table->insert($data)->save();
    }
}
