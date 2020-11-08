<?php
use Phinx\Migration\AbstractMigration;

class SecurityIncidentClosureDateMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('security_incidents')
            ->changeColumn('closure_date', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('security_incidents')
            ->changeColumn('closure_date', 'date', [
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }
}

