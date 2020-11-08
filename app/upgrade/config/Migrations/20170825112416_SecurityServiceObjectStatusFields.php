<?php
use Phinx\Migration\AbstractMigration;

class SecurityServiceObjectStatusFields extends AbstractMigration
{

    public function up()
    {

        $this->table('security_services')
            ->addColumn('audits_not_all_done', 'integer', [
                'after' => 'audits_all_done',
                'default' => null,
                'length' => 1,
                'null' => false,
            ])
            ->addColumn('maintenances_not_all_done', 'integer', [
                'after' => 'maintenances_all_done',
                'default' => null,
                'length' => 1,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('security_services')
            ->removeColumn('audits_not_all_done')
            ->removeColumn('maintenances_not_all_done')
            ->update();
    }
}

