<?php
use Phinx\Migration\AbstractMigration;

class SecurityIncidentAutoClose extends AbstractMigration
{

    public function up()
    {
        $this->table('security_incidents')
            ->addColumn('auto_close_incident', 'integer', [
                'after' => 'security_incident_status_id',
                'default' => '0',
                'length' => 1,
                'null' => true,
            ])
            ->update();

       
    }

    public function down()
    {
        $this->table('security_incidents')
            ->removeColumn('auto_close_incident')
            ->update();
    }
}

