<?php
use Phinx\Seed\AbstractSeed;

/**
 * AssetClassification seed.
 */
class AssetClassificationSeed extends AbstractSeed
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
                'name' => 'High',
                'criteria' => 'Legal consequences',
                'value' => '3',
                'asset_classification_type_id' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:37:31',
                'modified' => '2017-04-13 18:32:06',
            ],
            [
                'id' => '2',
                'name' => 'Low',
                'criteria' => 'Minor consequences',
                'value' => '2',
                'asset_classification_type_id' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:37:39',
                'modified' => '2017-04-13 18:32:13',
            ],
            [
                'id' => '3',
                'name' => 'Medium',
                'criteria' => 'Reputation consequences',
                'value' => '1',
                'asset_classification_type_id' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:37:47',
                'modified' => '2017-04-13 18:32:20',
            ],
            [
                'id' => '4',
                'name' => 'High',
                'criteria' => 'Legal consequences',
                'value' => '3',
                'asset_classification_type_id' => '2',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:37:55',
                'modified' => '2017-04-13 18:32:30',
            ],
            [
                'id' => '5',
                'name' => 'Medium',
                'criteria' => 'Reputation consequences',
                'value' => '2',
                'asset_classification_type_id' => '2',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:38:03',
                'modified' => '2017-04-13 18:32:36',
            ],
            [
                'id' => '6',
                'name' => 'Low',
                'criteria' => 'Minor consequences',
                'value' => '1',
                'asset_classification_type_id' => '2',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:38:08',
                'modified' => '2017-04-13 18:32:46',
            ],
            [
                'id' => '7',
                'name' => 'Low',
                'criteria' => 'Minor consequences',
                'value' => NULL,
                'asset_classification_type_id' => '3',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:38:49',
                'modified' => '2017-04-10 15:38:49',
            ],
            [
                'id' => '8',
                'name' => 'High',
                'criteria' => 'Legal consequences',
                'value' => '3',
                'asset_classification_type_id' => '3',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:38:55',
                'modified' => '2017-04-13 18:33:03',
            ],
            [
                'id' => '9',
                'name' => 'Medium',
                'criteria' => 'Reputation consequences',
                'value' => '2',
                'asset_classification_type_id' => '3',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-10 15:39:11',
                'modified' => '2017-04-13 18:33:17',
            ],
            [
                'id' => '10',
                'name' => 'High',
                'criteria' => 'NA',
                'value' => '3',
                'asset_classification_type_id' => '4',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-13 18:28:54',
                'modified' => '2017-04-13 18:28:54',
            ],
            [
                'id' => '11',
                'name' => 'Medium',
                'criteria' => 'NA',
                'value' => '2',
                'asset_classification_type_id' => '4',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-13 18:29:05',
                'modified' => '2017-04-13 18:29:05',
            ],
            [
                'id' => '12',
                'name' => 'Low',
                'criteria' => 'NA',
                'value' => '1',
                'asset_classification_type_id' => '4',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-13 18:29:14',
                'modified' => '2017-04-13 18:29:14',
            ],
            [
                'id' => '13',
                'name' => 'High',
                'criteria' => 'NA',
                'value' => '3',
                'asset_classification_type_id' => '5',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-13 18:29:24',
                'modified' => '2017-04-13 18:29:24',
            ],
            [
                'id' => '14',
                'name' => 'Medium',
                'criteria' => 'NA',
                'value' => '2',
                'asset_classification_type_id' => '5',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-13 18:29:35',
                'modified' => '2017-04-13 18:29:35',
            ],
            [
                'id' => '15',
                'name' => 'Low',
                'criteria' => 'NA',
                'value' => '1',
                'asset_classification_type_id' => '5',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-13 18:29:44',
                'modified' => '2017-04-13 18:29:44',
            ],
        ];

        $table = $this->table('asset_classifications');
        $table->insert($data)->save();
    }
}
