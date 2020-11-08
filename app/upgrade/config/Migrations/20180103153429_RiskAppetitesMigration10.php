<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration10 extends AbstractMigration
{

    public function up()
    {

        $this->table('business_continuities_risk_classifications')
            ->addColumn('type', 'integer', [
                'after' => 'risk_classification_id',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();

        $this->table('risk_classifications_third_party_risks')
            ->addColumn('type', 'integer', [
                'after' => 'third_party_risk_id',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('business_continuities_risk_classifications')
            ->removeColumn('type')
            ->update();

        $this->table('risk_classifications_third_party_risks')
            ->removeColumn('type')
            ->update();
    }
}

