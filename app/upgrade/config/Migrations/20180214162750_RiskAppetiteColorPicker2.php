<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetiteColorPicker2 extends AbstractMigration
{

    public function up()
    {

        $this->table('risk_appetite_thresholds')
            ->changeColumn('color', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('risk_appetite_thresholds')
            ->changeColumn('color', 'string', [
                'default' => null,
                'length' => 7,
                'null' => false,
            ])
            ->update();
    }
}

