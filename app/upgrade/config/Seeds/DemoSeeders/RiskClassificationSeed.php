<?php
use Phinx\Seed\AbstractSeed;

/**
 * RiskClassification seed.
 */
class RiskClassificationSeed extends AbstractSeed
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
                'name' => 'Low',
                'criteria' => 'Minor consequences',
                'value' => '1',
                'risk_classification_type_id' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:55:49',
                'modified' => '2017-04-11 12:55:49',
            ],
            [
                'id' => '2',
                'name' => 'Medium',
                'criteria' => 'Reputation consequences',
                'value' => '2',
                'risk_classification_type_id' => '2',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:55:54',
                'modified' => '2017-04-11 12:55:54',
            ],
            [
                'id' => '3',
                'name' => 'High',
                'criteria' => 'Legal consequences',
                'value' => '3',
                'risk_classification_type_id' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:56:01',
                'modified' => '2017-04-11 12:56:01',
            ],
            [
                'id' => '4',
                'name' => 'Medium',
                'criteria' => 'Reputation consequences',
                'value' => '2',
                'risk_classification_type_id' => '1',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:56:07',
                'modified' => '2017-04-11 12:56:07',
            ],
            [
                'id' => '5',
                'name' => 'High',
                'criteria' => 'Legal consequences',
                'value' => '3',
                'risk_classification_type_id' => '2',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:56:16',
                'modified' => '2017-04-11 12:56:16',
            ],
            [
                'id' => '6',
                'name' => 'Low',
                'criteria' => 'Minor consequences',
                'value' => '1',
                'risk_classification_type_id' => '2',
                'workflow_owner_id' => '1',
                'workflow_status' => '4',
                'created' => '2017-04-11 12:56:27',
                'modified' => '2017-04-11 12:56:27',
            ],
        ];

        $table = $this->table('risk_classifications');
        $table->insert($data)->save();
    }
}
