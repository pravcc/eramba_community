<?php
use Phinx\Seed\AbstractSeed;

/**
 * SecurityServiceType seed.
 */
class SecurityServiceTypeSeed extends AbstractSeed
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
                'id' => '2',
                'name' => 'Design',
            ],
            [
                'id' => '4',
                'name' => 'Production',
            ],
        ];

        $table = $this->table('security_service_types');
        $table->insert($data)->save();
    }
}
