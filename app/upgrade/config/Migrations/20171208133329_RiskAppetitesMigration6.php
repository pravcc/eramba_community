<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration6 extends AbstractMigration
{

    public function up()
    {

        $this->table('risk_appetite_threshold_risk_classifications')
            ->removeColumn('type')
            ->update();

        $this->table('risk_appetite_thresholds')
            ->addColumn('type', 'integer', [
                'after' => 'color',
                'default' => '1',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('risk_appetite_threshold_risk_classifications')
            ->addColumn('type', 'integer', [
                'after' => 'risk_classification_id',
                'default' => '1',
                'length' => 3,
                'null' => false,
            ])
            ->update();

        $this->table('risk_appetite_thresholds')
            ->removeColumn('type')
            ->update();
    }
}

