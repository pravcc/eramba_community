<?php
use Phinx\Migration\AbstractMigration;

class BusinessUnitSoftDelete extends AbstractMigration
{

    public function up()
    {

        $this->table('business_units')
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

        $this->table('business_units')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

