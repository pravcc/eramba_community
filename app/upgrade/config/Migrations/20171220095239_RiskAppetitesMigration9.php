<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration9 extends AbstractMigration
{

    public function up()
    {

        $this->table('risks')
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

        $this->table('risks')
            ->removeColumn('residual_risk_formula')
            ->update();
    }
}

