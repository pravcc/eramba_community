<?php
use Phinx\Seed\AbstractSeed;

/**
 * ThirdPartyAuditOvertimeGraph seed.
 */
class ThirdPartyAuditOvertimeGraphSeed extends AbstractSeed
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
                'third_party_id' => '4',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
            [
                'id' => '2',
                'third_party_id' => '5',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1491983265',
                'created' => '2017-04-12 07:47:45',
            ],
            [
                'id' => '3',
                'third_party_id' => '5',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1492041611',
                'created' => '2017-04-13 00:00:11',
            ],
            [
                'id' => '4',
                'third_party_id' => '5',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1492128012',
                'created' => '2017-04-14 00:00:12',
            ],
            [
                'id' => '5',
                'third_party_id' => '5',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1492214414',
                'created' => '2017-04-15 00:00:14',
            ],
            [
                'id' => '6',
                'third_party_id' => '5',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1492300810',
                'created' => '2017-04-16 00:00:10',
            ],
            [
                'id' => '7',
                'third_party_id' => '5',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1492387215',
                'created' => '2017-04-17 00:00:15',
            ],
            [
                'id' => '8',
                'third_party_id' => '5',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1492473612',
                'created' => '2017-04-18 00:00:12',
            ],
            [
                'id' => '9',
                'third_party_id' => '5',
                'open' => NULL,
                'closed' => NULL,
                'expired' => NULL,
                'no_evidence' => '0',
                'waiting_evidence' => '100',
                'provided_evidence' => '0',
                'timestamp' => '1492560013',
                'created' => '2017-04-19 00:00:13',
            ],
        ];

        $table = $this->table('third_party_audit_overtime_graphs');
        $table->insert($data)->save();
    }
}
