<?php
use Phinx\Seed\AbstractSeed;

/**
 * LogSecurityPolicy seed.
 */
class LogSecurityPolicySeed extends AbstractSeed
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
                'security_policy_id' => '29',
                'index' => 'Information Security Policy',
                'short_description' => 'Our risk policy that describes overall governance, risk practices, Etc.',
                'description' => '',
                'document_type' => 'policy',
                'version' => '4.5',
                'published_date' => '2017-04-10',
                'next_review_date' => '2017-06-22',
                'permission' => 'public',
                'ldap_connector_id' => NULL,
                'asset_label_id' => NULL,
                'user_edit_id' => '1',
                'created' => '2017-04-10 14:15:38',
            ],
            [
                'id' => '2',
                'security_policy_id' => '1',
                'index' => 'Acceptable Encryption Policy',
                'short_description' => 'Outlines the requirement around which encryption algorithms (e.g. received substantial public review and have been proven to work effectively) are acc',
                'description' => NULL,
                'document_type' => 'policy',
                'version' => '1',
                'published_date' => '2015-11-30',
                'next_review_date' => '2017-12-31',
                'permission' => 'public',
                'ldap_connector_id' => NULL,
                'asset_label_id' => NULL,
                'user_edit_id' => '1',
                'created' => '2017-04-10 13:28:10',
            ],
            [
                'id' => '3',
                'security_policy_id' => '1',
                'index' => 'Acceptable Encryption Policy',
                'short_description' => 'Outlines the requirement around which encryption algorithms (e.g. received substantial public review and have been proven to work effectively) are acc',
                'description' => NULL,
                'document_type' => 'policy',
                'version' => '2.1',
                'published_date' => '2015-11-30',
                'next_review_date' => '2017-12-31',
                'permission' => 'public',
                'ldap_connector_id' => NULL,
                'asset_label_id' => NULL,
                'user_edit_id' => '1',
                'created' => '2017-04-10 13:28:10',
            ],
        ];

        $table = $this->table('log_security_policies');
        $table->insert($data)->save();
    }
}
