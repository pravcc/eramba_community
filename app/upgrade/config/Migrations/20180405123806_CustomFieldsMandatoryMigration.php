<?php
use Phinx\Migration\AbstractMigration;

class CustomFieldsMandatoryMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('custom_fields')
            ->addColumn('mandatory', 'integer', [
                'after' => 'type',
                'default' => '0',
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('custom_fields')
            ->removeColumn('mandatory')
            ->update();
    }
}

