<?php
use Phinx\Seed\AbstractSeed;

/**
 * Comment seed.
 */
class CommentSeed extends AbstractSeed
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
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '10',
                'message' => 'I dont understand what you need.',
                'user_id' => '4',
                'created' => '2017-04-11 14:02:01',
                'modified' => '2017-04-11 14:02:01',
            ],
            [
                'id' => '2',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '10',
                'message' => 'I dont undertstand either! ',
                'user_id' => '1',
                'created' => '2017-04-11 14:02:38',
                'modified' => '2017-04-11 14:02:38',
            ],
            [
                'id' => '3',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '8',
                'message' => 'Some comment',
                'user_id' => '3',
                'created' => '2017-04-12 21:29:17',
                'modified' => '2017-04-12 21:29:17',
            ],
        ];

        $table = $this->table('comments');
        $table->insert($data)->save();
    }
}
