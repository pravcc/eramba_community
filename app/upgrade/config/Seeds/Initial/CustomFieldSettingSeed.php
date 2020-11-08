<?php
use Phinx\Seed\AbstractSeed;

/**
 * CustomFieldSetting seed.
 */
class CustomFieldSettingSeed extends AbstractSeed
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
                'model' => 'SecurityService',
                'status' => '0',
            ],
            [
                'id' => '2',
                'model' => 'SecurityServiceAudit',
                'status' => '0',
            ],
            [
                'id' => '3',
                'model' => 'SecurityServiceMaintenance',
                'status' => '0',
            ],
            [
                'id' => '4',
                'model' => 'BusinessUnit',
                'status' => '0',
            ],
            [
                'id' => '5',
                'model' => 'Process',
                'status' => '0',
            ],
            [
                'id' => '6',
                'model' => 'ThirdParty',
                'status' => '0',
            ],
            [
                'id' => '7',
                'model' => 'Asset',
                'status' => '0',
            ],
            [
                'id' => '8',
                'model' => 'Risk',
                'status' => '0',
            ],
            [
                'id' => '9',
                'model' => 'ThirdPartyRisk',
                'status' => '0',
            ],
            [
                'id' => '10',
                'model' => 'BusinessContinuity',
                'status' => '0',
            ],
        ];

        $table = $this->table('custom_field_settings');
        $table->insert($data)->save();
    }
}
