<?php
use Phinx\Seed\AbstractSeed;

/**
 * RisksSecurityPolicy seed.
 */
class RisksSecurityPolicySeed extends AbstractSeed
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
                'id' => '5',
                'risk_id' => '2',
                'security_policy_id' => '4',
                'type' => 'incident',
                'document_type' => 'procedure',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 13:07:37',
            ],
            [
                'id' => '6',
                'risk_id' => '2',
                'security_policy_id' => '11',
                'type' => 'treatment',
                'document_type' => 'policy',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 13:07:37',
            ],
            [
                'id' => '7',
                'risk_id' => '2',
                'security_policy_id' => '16',
                'type' => 'treatment',
                'document_type' => 'policy',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 13:07:37',
            ],
            [
                'id' => '8',
                'risk_id' => '2',
                'security_policy_id' => '40',
                'type' => 'treatment',
                'document_type' => 'standard',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 13:07:37',
            ],
            [
                'id' => '9',
                'risk_id' => '2',
                'security_policy_id' => '10',
                'type' => 'treatment',
                'document_type' => 'standard',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 13:07:37',
            ],
            [
                'id' => '10',
                'risk_id' => '1',
                'security_policy_id' => '19',
                'type' => 'treatment',
                'document_type' => 'policy',
                'risk_type' => 'third-party-risk',
                'created' => '2017-04-11 13:25:23',
            ],
            [
                'id' => '11',
                'risk_id' => '1',
                'security_policy_id' => '30',
                'type' => 'treatment',
                'document_type' => 'standard',
                'risk_type' => 'third-party-risk',
                'created' => '2017-04-11 13:25:23',
            ],
            [
                'id' => '12',
                'risk_id' => '1',
                'security_policy_id' => '4',
                'type' => 'incident',
                'document_type' => 'procedure',
                'risk_type' => 'third-party-risk',
                'created' => '2017-04-11 13:25:23',
            ],
            [
                'id' => '13',
                'risk_id' => '1',
                'security_policy_id' => '4',
                'type' => 'incident',
                'document_type' => 'procedure',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 14:09:09',
            ],
            [
                'id' => '14',
                'risk_id' => '1',
                'security_policy_id' => '1',
                'type' => 'treatment',
                'document_type' => 'policy',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 14:09:09',
            ],
            [
                'id' => '15',
                'risk_id' => '1',
                'security_policy_id' => '42',
                'type' => 'treatment',
                'document_type' => 'policy',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 14:09:09',
            ],
            [
                'id' => '16',
                'risk_id' => '1',
                'security_policy_id' => '13',
                'type' => 'treatment',
                'document_type' => 'policy',
                'risk_type' => 'asset-risk',
                'created' => '2017-04-11 14:09:09',
            ],
        ];

        $table = $this->table('risks_security_policies');
        $table->insert($data)->save();
    }
}
