<?php
use Phinx\Migration\AbstractMigration;

class ServiceContractTrash extends AbstractMigration
{

    public function up()
    {

        $this->table('service_contracts')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('service_contracts')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

