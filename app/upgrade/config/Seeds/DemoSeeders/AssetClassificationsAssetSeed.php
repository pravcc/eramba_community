<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetClassificationsAsset seed.
 */
class AssetClassificationsAssetSeed extends AbstractSeed
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
                'id' => '4',
                'asset_classification_id' => '2',
                'asset_id' => '2',
            ],
            [
                'id' => '5',
                'asset_classification_id' => '5',
                'asset_id' => '2',
            ],
            [
                'id' => '6',
                'asset_classification_id' => '9',
                'asset_id' => '2',
            ],
            [
                'id' => '7',
                'asset_classification_id' => '1',
                'asset_id' => '3',
            ],
            [
                'id' => '8',
                'asset_classification_id' => '6',
                'asset_id' => '3',
            ],
            [
                'id' => '9',
                'asset_classification_id' => '8',
                'asset_id' => '3',
            ],
            [
                'id' => '10',
                'asset_classification_id' => '1',
                'asset_id' => '4',
            ],
            [
                'id' => '11',
                'asset_classification_id' => '4',
                'asset_id' => '4',
            ],
            [
                'id' => '12',
                'asset_classification_id' => '7',
                'asset_id' => '4',
            ],
            [
                'id' => '13',
                'asset_classification_id' => '1',
                'asset_id' => '5',
            ],
            [
                'id' => '14',
                'asset_classification_id' => '5',
                'asset_id' => '5',
            ],
            [
                'id' => '15',
                'asset_classification_id' => '7',
                'asset_id' => '5',
            ],
            [
                'id' => '16',
                'asset_classification_id' => '2',
                'asset_id' => '6',
            ],
            [
                'id' => '17',
                'asset_classification_id' => '5',
                'asset_id' => '6',
            ],
            [
                'id' => '18',
                'asset_classification_id' => '7',
                'asset_id' => '6',
            ],
            [
                'id' => '19',
                'asset_classification_id' => '2',
                'asset_id' => '7',
            ],
            [
                'id' => '20',
                'asset_classification_id' => '5',
                'asset_id' => '7',
            ],
            [
                'id' => '21',
                'asset_classification_id' => '8',
                'asset_id' => '7',
            ],
            [
                'id' => '22',
                'asset_classification_id' => '1',
                'asset_id' => '8',
            ],
            [
                'id' => '23',
                'asset_classification_id' => '5',
                'asset_id' => '8',
            ],
            [
                'id' => '24',
                'asset_classification_id' => '8',
                'asset_id' => '8',
            ],
            [
                'id' => '25',
                'asset_classification_id' => '2',
                'asset_id' => '9',
            ],
            [
                'id' => '26',
                'asset_classification_id' => '5',
                'asset_id' => '9',
            ],
            [
                'id' => '27',
                'asset_classification_id' => '9',
                'asset_id' => '9',
            ],
            [
                'id' => '28',
                'asset_classification_id' => '1',
                'asset_id' => '10',
            ],
            [
                'id' => '29',
                'asset_classification_id' => '5',
                'asset_id' => '10',
            ],
            [
                'id' => '30',
                'asset_classification_id' => '8',
                'asset_id' => '10',
            ],
            [
                'id' => '31',
                'asset_classification_id' => '1',
                'asset_id' => '11',
            ],
            [
                'id' => '32',
                'asset_classification_id' => '5',
                'asset_id' => '11',
            ],
            [
                'id' => '33',
                'asset_classification_id' => '8',
                'asset_id' => '11',
            ],
            [
                'id' => '34',
                'asset_classification_id' => '2',
                'asset_id' => '12',
            ],
            [
                'id' => '35',
                'asset_classification_id' => '5',
                'asset_id' => '12',
            ],
            [
                'id' => '36',
                'asset_classification_id' => '8',
                'asset_id' => '12',
            ],
            [
                'id' => '37',
                'asset_classification_id' => '1',
                'asset_id' => '13',
            ],
            [
                'id' => '38',
                'asset_classification_id' => '6',
                'asset_id' => '13',
            ],
            [
                'id' => '39',
                'asset_classification_id' => '8',
                'asset_id' => '13',
            ],
            [
                'id' => '45',
                'asset_classification_id' => '1',
                'asset_id' => '1',
            ],
            [
                'id' => '46',
                'asset_classification_id' => '6',
                'asset_id' => '1',
            ],
            [
                'id' => '47',
                'asset_classification_id' => '8',
                'asset_id' => '1',
            ],
            [
                'id' => '48',
                'asset_classification_id' => '11',
                'asset_id' => '1',
            ],
            [
                'id' => '49',
                'asset_classification_id' => '15',
                'asset_id' => '1',
            ],
        ];

        $table = $this->table('asset_classifications_assets');
        $table->insert($data)->save();
    }
}
