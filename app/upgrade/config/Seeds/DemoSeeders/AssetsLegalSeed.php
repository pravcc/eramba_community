<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetsLegal seed.
 */
class AssetsLegalSeed extends AbstractSeed
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
                'id' => '7',
                'asset_id' => '3',
                'legal_id' => '1',
            ],
            [
                'id' => '8',
                'asset_id' => '4',
                'legal_id' => '2',
            ],
            [
                'id' => '9',
                'asset_id' => '5',
                'legal_id' => '1',
            ],
            [
                'id' => '10',
                'asset_id' => '6',
                'legal_id' => '2',
            ],
            [
                'id' => '12',
                'asset_id' => '1',
                'legal_id' => '1',
            ],
        ];

        $table = $this->table('assets_legals');
        $table->insert($data)->save();
    }
}
