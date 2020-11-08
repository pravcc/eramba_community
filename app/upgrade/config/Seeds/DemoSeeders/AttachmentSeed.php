<?php
use Phinx\Seed\AbstractSeed;

/**
 * Attachment seed.
 */
class AttachmentSeed extends AbstractSeed
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
                'model' => 'SecurityPolicyReview',
                'foreign_key' => '30',
                'filename' => '/files/uploads/policy.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '85450',
                'description' => '',
                'user_id' => '1',
                'created' => '2017-04-10 14:16:54',
                'modified' => '2017-04-10 14:16:54',
            ],
            [
                'id' => '2',
                'model' => 'SecurityPolicyReview',
                'foreign_key' => '34',
                'filename' => '/files/uploads/Rental-Voucher-Rentalcars.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '165811',
                'description' => '',
                'user_id' => '1',
                'created' => '2017-04-11 12:42:28',
                'modified' => '2017-04-11 12:42:28',
            ],
            [
                'id' => '3',
                'model' => 'SecurityServiceAudit',
                'foreign_key' => '26',
                'filename' => '/files/uploads/audit-evidence.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '165811',
                'description' => '',
                'user_id' => '1',
                'created' => '2017-04-11 12:45:15',
                'modified' => '2017-04-11 12:45:15',
            ],
            [
                'id' => '4',
                'model' => 'SecurityServiceAudit',
                'foreign_key' => '2',
                'filename' => '/files/uploads/audit-evidence.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '165811',
                'description' => '',
                'user_id' => '1',
                'created' => '2017-04-11 12:47:10',
                'modified' => '2017-04-11 12:47:10',
            ],
            [
                'id' => '5',
                'model' => 'SecurityServiceMaintenance',
                'foreign_key' => '1',
                'filename' => '/files/uploads/audit-evidence.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '165811',
                'description' => '',
                'user_id' => '1',
                'created' => '2017-04-11 12:47:41',
                'modified' => '2017-04-11 12:47:41',
            ],
            [
                'id' => '6',
                'model' => 'SecurityServiceAudit',
                'foreign_key' => '4',
                'filename' => '/files/uploads/audit-evidence.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '165811',
                'description' => '',
                'user_id' => '1',
                'created' => '2017-04-11 12:49:05',
                'modified' => '2017-04-11 12:49:05',
            ],
            [
                'id' => '7',
                'model' => 'SecurityServiceAudit',
                'foreign_key' => '7',
                'filename' => '/files/uploads/audit-evidence.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '165811',
                'description' => '',
                'user_id' => '1',
                'created' => '2017-04-11 12:50:02',
                'modified' => '2017-04-11 12:50:02',
            ],
            [
                'id' => '8',
                'model' => 'SecurityServiceAudit',
                'foreign_key' => '8',
                'filename' => '/files/uploads/audit-evidence.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '165811',
                'description' => '',
                'user_id' => '1',
                'created' => '2017-04-11 12:51:32',
                'modified' => '2017-04-11 12:51:32',
            ],
            [
                'id' => '9',
                'model' => 'ComplianceAuditSetting',
                'foreign_key' => '2',
                'filename' => '/files/uploads/audit-evidence.pdf',
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
                'file_size' => '165811',
                'description' => '',
                'user_id' => '3',
                'created' => '2017-04-12 21:29:28',
                'modified' => '2017-04-12 21:29:28',
            ],
        ];

        $table = $this->table('attachments');
        $table->insert($data)->save();
    }
}
