<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration11 extends AbstractMigration
{

    public function up()
    {

        $this->table('business_continuities')
            ->addColumn('risk_score_formula', 'text', [
                'after' => 'risk_score',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('residual_risk_formula', 'text', [
                'after' => 'residual_risk',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();

        $this->table('third_party_risks')
            ->addColumn('residual_risk_formula', 'text', [
                'after' => 'residual_risk',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('business_continuities')
            ->removeColumn('risk_score_formula')
            ->removeColumn('residual_risk_formula')
            ->update();

        $this->table('third_party_risks')
            ->removeColumn('residual_risk_formula')
            ->update();
    }
}

