<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceManagementsSecurityService seed.
 */
class ComplianceManagementsSecurityServiceSeed extends AbstractSeed
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
                'compliance_management_id' => '2',
                'security_service_id' => '1',
            ],
            [
                'id' => '2',
                'compliance_management_id' => '2',
                'security_service_id' => '20',
            ],
            [
                'id' => '3',
                'compliance_management_id' => '2',
                'security_service_id' => '8',
            ],
            [
                'id' => '4',
                'compliance_management_id' => '2',
                'security_service_id' => '19',
            ],
            [
                'id' => '5',
                'compliance_management_id' => '2',
                'security_service_id' => '14',
            ],
            [
                'id' => '6',
                'compliance_management_id' => '2',
                'security_service_id' => '21',
            ],
            [
                'id' => '7',
                'compliance_management_id' => '3',
                'security_service_id' => '1',
            ],
            [
                'id' => '8',
                'compliance_management_id' => '4',
                'security_service_id' => '25',
            ],
            [
                'id' => '9',
                'compliance_management_id' => '5',
                'security_service_id' => '25',
            ],
            [
                'id' => '10',
                'compliance_management_id' => '7',
                'security_service_id' => '11',
            ],
            [
                'id' => '11',
                'compliance_management_id' => '7',
                'security_service_id' => '17',
            ],
            [
                'id' => '12',
                'compliance_management_id' => '8',
                'security_service_id' => '24',
            ],
            [
                'id' => '13',
                'compliance_management_id' => '9',
                'security_service_id' => '11',
            ],
            [
                'id' => '14',
                'compliance_management_id' => '9',
                'security_service_id' => '17',
            ],
            [
                'id' => '15',
                'compliance_management_id' => '10',
                'security_service_id' => '11',
            ],
            [
                'id' => '16',
                'compliance_management_id' => '10',
                'security_service_id' => '17',
            ],
            [
                'id' => '17',
                'compliance_management_id' => '11',
                'security_service_id' => '11',
            ],
            [
                'id' => '18',
                'compliance_management_id' => '12',
                'security_service_id' => '25',
            ],
            [
                'id' => '19',
                'compliance_management_id' => '13',
                'security_service_id' => '7',
            ],
            [
                'id' => '20',
                'compliance_management_id' => '13',
                'security_service_id' => '10',
            ],
            [
                'id' => '21',
                'compliance_management_id' => '13',
                'security_service_id' => '4',
            ],
            [
                'id' => '22',
                'compliance_management_id' => '14',
                'security_service_id' => '11',
            ],
            [
                'id' => '23',
                'compliance_management_id' => '14',
                'security_service_id' => '17',
            ],
            [
                'id' => '24',
                'compliance_management_id' => '24',
                'security_service_id' => '24',
            ],
            [
                'id' => '25',
                'compliance_management_id' => '25',
                'security_service_id' => '24',
            ],
            [
                'id' => '26',
                'compliance_management_id' => '26',
                'security_service_id' => '9',
            ],
            [
                'id' => '27',
                'compliance_management_id' => '27',
                'security_service_id' => '9',
            ],
            [
                'id' => '28',
                'compliance_management_id' => '28',
                'security_service_id' => '6',
            ],
            [
                'id' => '29',
                'compliance_management_id' => '29',
                'security_service_id' => '6',
            ],
        ];

        $table = $this->table('compliance_managements_security_services');
        $table->insert($data)->save();
    }
}
