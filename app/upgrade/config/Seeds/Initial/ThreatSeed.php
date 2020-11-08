<?php
use Phinx\Seed\AbstractSeed;

/**
 * Threat seed.
 */
class ThreatSeed extends AbstractSeed
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
                'name' => 'Intentional Complot',
            ],
            [
                'id' => '2',
                'name' => 'Pandemic Issues',
            ],
            [
                'id' => '3',
                'name' => 'Strikes',
            ],
            [
                'id' => '4',
                'name' => 'Unintentional Loss of Equipment',
            ],
            [
                'id' => '5',
                'name' => 'Intentional Theft of Equipment',
            ],
            [
                'id' => '6',
                'name' => 'Unintentional Loss of Information',
            ],
            [
                'id' => '7',
                'name' => 'Intentional Theft of Information',
            ],
            [
                'id' => '8',
                'name' => 'Remote Exploit',
            ],
            [
                'id' => '9',
                'name' => 'Abuse of Service',
            ],
            [
                'id' => '10',
                'name' => 'Web Application Attack',
            ],
            [
                'id' => '11',
                'name' => 'Network Attack',
            ],
            [
                'id' => '12',
                'name' => 'Sniffing',
            ],
            [
                'id' => '13',
                'name' => 'Phishing',
            ],
            [
                'id' => '14',
                'name' => 'Malware/Trojan Distribution',
            ],
            [
                'id' => '15',
                'name' => 'Viruses',
            ],
            [
                'id' => '16',
                'name' => 'Copyright Infrigment',
            ],
            [
                'id' => '17',
                'name' => 'Social Engineering',
            ],
            [
                'id' => '18',
                'name' => 'Natural Disasters',
            ],
            [
                'id' => '19',
                'name' => 'Fire',
            ],
            [
                'id' => '20',
                'name' => 'Flooding',
            ],
            [
                'id' => '21',
                'name' => 'Ilegal Infiltration',
            ],
            [
                'id' => '22',
                'name' => 'DOS Attack',
            ],
            [
                'id' => '23',
                'name' => 'Brute Force Attack',
            ],
            [
                'id' => '24',
                'name' => 'Tampering',
            ],
            [
                'id' => '25',
                'name' => 'Tunneling',
            ],
            [
                'id' => '26',
                'name' => 'Man in the Middle',
            ],
            [
                'id' => '27',
                'name' => 'Fraud',
            ],
            [
                'id' => '28',
                'name' => 'Other',
            ],
            [
                'id' => '30',
                'name' => 'Terrorist Attack',
            ],
            [
                'id' => '31',
                'name' => 'Floodings',
            ],
            [
                'id' => '32',
                'name' => 'Third Party Intrusion',
            ],
            [
                'id' => '33',
                'name' => 'Abuse of Priviledge',
            ],
            [
                'id' => '34',
                'name' => 'Unauthorised records',
            ],
            [
                'id' => '35',
                'name' => 'Spying',
            ],
        ];

        $table = $this->table('threats');
        $table->insert($data)->save();
    }
}
