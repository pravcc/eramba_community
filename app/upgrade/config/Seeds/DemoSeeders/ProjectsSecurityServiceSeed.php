<?php
use Phinx\Seed\AbstractSeed;

/**
 * ProjectsSecurityService seed.
 */
class ProjectsSecurityServiceSeed extends AbstractSeed
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
                'project_id' => '1',
                'security_service_id' => '2',
                'created' => '2017-04-11 16:56:38',
            ],
        ];

        $table = $this->table('projects_security_services');
        $table->insert($data)->save();
    }
}
