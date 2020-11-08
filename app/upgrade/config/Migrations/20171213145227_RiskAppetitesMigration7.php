<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration7 extends AbstractMigration
{

    public function up()
    {

        $this->table('risk_classifications_risks')
            ->addColumn('type', 'integer', [
                'after' => 'risk_id',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('risk_classifications_risks')
            ->removeColumn('type')
            ->update();
    }
}

