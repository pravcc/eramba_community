<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdParty seed.
 */
class ThirdPartySeed extends AbstractSeed
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
                'name' => 'None',
                'description' => '',
                'third_party_type_id' => NULL,
                'security_incident_count' => '0',
                'security_incident_open_count' => '0',
                'service_contract_count' => '0',
                'workflow_status' => '0',
                'workflow_owner_id' => NULL,
                '_hidden' => '1',
                'created' => '2015-12-19 00:00:00',
                'modified' => '2015-12-19 00:00:00',
            ],
            [
                'id' => '2',
                'name' => 'ISO 27001',
                'description' => '',
                'third_party_type_id' => '3',
                'security_incident_count' => '0',
                'security_incident_open_count' => '0',
                'service_contract_count' => '0',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-10 13:47:32',
                'modified' => '2017-04-10 13:47:32',
            ],
            [
                'id' => '3',
                'name' => 'HIPAA-HITRUST 8.0',
                'description' => '',
                'third_party_type_id' => '3',
                'security_incident_count' => '0',
                'security_incident_open_count' => '0',
                'service_contract_count' => '0',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-10 13:51:05',
                'modified' => '2017-04-10 13:51:05',
            ],
            [
                'id' => '4',
                'name' => 'PCI-DSS 3.2',
                'description' => '',
                'third_party_type_id' => '3',
                'security_incident_count' => '0',
                'security_incident_open_count' => '0',
                'service_contract_count' => '0',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-10 13:55:26',
                'modified' => '2017-04-10 13:55:26',
            ],
            [
                'id' => '5',
                'name' => 'HIPAA-HITRUST 8.0 (Short Version)',
                'description' => '',
                'third_party_type_id' => '3',
                'security_incident_count' => '0',
                'security_incident_open_count' => '0',
                'service_contract_count' => '0',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-10 15:57:55',
                'modified' => '2017-04-10 15:57:55',
            ],
            [
                'id' => '6',
                'name' => 'Contractors',
                'description' => '',
                'third_party_type_id' => '2',
                'security_incident_count' => '0',
                'security_incident_open_count' => '0',
                'service_contract_count' => '0',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-11 13:22:53',
                'modified' => '2017-04-11 13:22:53',
            ],
            [
                'id' => '7',
                'name' => 'SWIFT CSP',
                'description' => '',
                'third_party_type_id' => '3',
                'security_incident_count' => '0',
                'security_incident_open_count' => '0',
                'service_contract_count' => '0',
                'workflow_status' => '4',
                'workflow_owner_id' => '1',
                '_hidden' => '0',
                'created' => '2017-04-12 07:58:34',
                'modified' => '2017-04-12 07:58:34',
            ],
        ];

        $table = $this->table('third_parties');
        $table->insert($data)->save();
    }
}
