<?php
use Phinx\Seed\AbstractSeed;

/**
 * ProjectStatus seed.
 */
class ProjectStatusSeed extends AbstractSeed
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
                'name' => 'Planned',
            ],
            [
                'id' => '2',
                'name' => 'Ongoing',
            ],
            [
                'id' => '3',
                'name' => 'Completed',
            ],
        ];

        $table = $this->table('project_statuses');
        $table->insert($data)->save();
    }
}
