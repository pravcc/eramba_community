<?php
use Phinx\Migration\AbstractMigration;

class RiskDescriptionField extends AbstractMigration
{

    public function up()
    {

        $this->table('business_continuities')
            ->addColumn('description', 'text', [
                'after' => 'vulnerabilities',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('risks')
            ->addColumn('description', 'text', [
                'after' => 'vulnerabilities',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('third_party_risks')
            ->addColumn('description', 'text', [
                'after' => 'vulnerabilities',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('business_continuities')
            ->removeColumn('description')
            ->update();

        $this->table('risks')
            ->removeColumn('description')
            ->update();

        $this->table('third_party_risks')
            ->removeColumn('description')
            ->update();
    }
}

