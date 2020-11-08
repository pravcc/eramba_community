<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration3 extends AbstractMigration
{

    public function up()
    {

        $this->table('risk_appetite_thresholds')
            ->changeColumn('risk_classification_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('risk_appetite_thresholds')
            ->changeColumn('risk_classification_id', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }
}

