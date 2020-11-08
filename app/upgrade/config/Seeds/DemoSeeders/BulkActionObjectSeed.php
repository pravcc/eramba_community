<?php
use Phinx\Seed\AbstractSeed;

/**
 * BulkActionObject seed.
 */
class BulkActionObjectSeed extends AbstractSeed
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
                'bulk_action_id' => '1',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '268',
            ],
            [
                'id' => '2',
                'bulk_action_id' => '1',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '269',
            ],
            [
                'id' => '3',
                'bulk_action_id' => '1',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '270',
            ],
            [
                'id' => '4',
                'bulk_action_id' => '1',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '271',
            ],
            [
                'id' => '5',
                'bulk_action_id' => '1',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '272',
            ],
            [
                'id' => '6',
                'bulk_action_id' => '1',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '273',
            ],
            [
                'id' => '7',
                'bulk_action_id' => '1',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '274',
            ],
            [
                'id' => '8',
                'bulk_action_id' => '1',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '275',
            ],
        ];

        $table = $this->table('bulk_action_objects');
        $table->insert($data)->save();
    }
}
