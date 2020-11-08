<?php
use Phinx\Seed\AbstractSeed;

/**
 * ComplianceManagementsSecurityPolicy seed.
 */
class ComplianceManagementsSecurityPolicySeed extends AbstractSeed
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
                'compliance_management_id' => '1',
                'security_policy_id' => '29',
            ],
            [
                'id' => '2',
                'compliance_management_id' => '2',
                'security_policy_id' => '16',
            ],
            [
                'id' => '3',
                'compliance_management_id' => '3',
                'security_policy_id' => '16',
            ],
            [
                'id' => '4',
                'compliance_management_id' => '4',
                'security_policy_id' => '10',
            ],
            [
                'id' => '5',
                'compliance_management_id' => '4',
                'security_policy_id' => '11',
            ],
            [
                'id' => '6',
                'compliance_management_id' => '5',
                'security_policy_id' => '10',
            ],
            [
                'id' => '7',
                'compliance_management_id' => '5',
                'security_policy_id' => '11',
            ],
            [
                'id' => '8',
                'compliance_management_id' => '6',
                'security_policy_id' => '3',
            ],
            [
                'id' => '9',
                'compliance_management_id' => '7',
                'security_policy_id' => '19',
            ],
            [
                'id' => '10',
                'compliance_management_id' => '8',
                'security_policy_id' => '16',
            ],
            [
                'id' => '11',
                'compliance_management_id' => '8',
                'security_policy_id' => '17',
            ],
            [
                'id' => '12',
                'compliance_management_id' => '9',
                'security_policy_id' => '19',
            ],
            [
                'id' => '13',
                'compliance_management_id' => '10',
                'security_policy_id' => '30',
            ],
            [
                'id' => '14',
                'compliance_management_id' => '10',
                'security_policy_id' => '19',
            ],
            [
                'id' => '15',
                'compliance_management_id' => '11',
                'security_policy_id' => '30',
            ],
            [
                'id' => '16',
                'compliance_management_id' => '11',
                'security_policy_id' => '19',
            ],
            [
                'id' => '17',
                'compliance_management_id' => '12',
                'security_policy_id' => '10',
            ],
            [
                'id' => '18',
                'compliance_management_id' => '12',
                'security_policy_id' => '11',
            ],
            [
                'id' => '19',
                'compliance_management_id' => '14',
                'security_policy_id' => '30',
            ],
            [
                'id' => '20',
                'compliance_management_id' => '14',
                'security_policy_id' => '19',
            ],
            [
                'id' => '21',
                'compliance_management_id' => '16',
                'security_policy_id' => '31',
            ],
            [
                'id' => '22',
                'compliance_management_id' => '17',
                'security_policy_id' => '30',
            ],
            [
                'id' => '23',
                'compliance_management_id' => '18',
                'security_policy_id' => '31',
            ],
            [
                'id' => '24',
                'compliance_management_id' => '19',
                'security_policy_id' => '29',
            ],
            [
                'id' => '25',
                'compliance_management_id' => '20',
                'security_policy_id' => '29',
            ],
            [
                'id' => '26',
                'compliance_management_id' => '21',
                'security_policy_id' => '29',
            ],
            [
                'id' => '27',
                'compliance_management_id' => '22',
                'security_policy_id' => '29',
            ],
            [
                'id' => '28',
                'compliance_management_id' => '23',
                'security_policy_id' => '29',
            ],
            [
                'id' => '29',
                'compliance_management_id' => '24',
                'security_policy_id' => '16',
            ],
            [
                'id' => '30',
                'compliance_management_id' => '24',
                'security_policy_id' => '17',
            ],
            [
                'id' => '31',
                'compliance_management_id' => '25',
                'security_policy_id' => '16',
            ],
            [
                'id' => '32',
                'compliance_management_id' => '25',
                'security_policy_id' => '17',
            ],
            [
                'id' => '33',
                'compliance_management_id' => '26',
                'security_policy_id' => '4',
            ],
            [
                'id' => '34',
                'compliance_management_id' => '26',
                'security_policy_id' => '8',
            ],
            [
                'id' => '35',
                'compliance_management_id' => '28',
                'security_policy_id' => '13',
            ],
            [
                'id' => '36',
                'compliance_management_id' => '29',
                'security_policy_id' => '2',
            ],
            [
                'id' => '37',
                'compliance_management_id' => '30',
                'security_policy_id' => '2',
            ],
        ];

        $table = $this->table('compliance_managements_security_policies');
        $table->insert($data)->save();
    }
}
