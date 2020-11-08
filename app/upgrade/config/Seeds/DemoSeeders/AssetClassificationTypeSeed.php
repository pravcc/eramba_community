<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetClassificationType seed.
 */
class AssetClassificationTypeSeed extends AbstractSeed
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
                'name' => 'Availability',
                'asset_classification_count' => '3',
            ],
            [
                'id' => '2',
                'name' => 'Confidentiality',
                'asset_classification_count' => '3',
            ],
            [
                'id' => '3',
                'name' => 'Integrity',
                'asset_classification_count' => '3',
            ],
            [
                'id' => '4',
                'name' => 'Reputation',
                'asset_classification_count' => '3',
            ],
            [
                'id' => '5',
                'name' => 'Value',
                'asset_classification_count' => '3',
            ],
        ];

        $table = $this->table('asset_classification_types');
        $table->insert($data)->save();
    }
}
