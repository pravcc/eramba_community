<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration2 extends AbstractMigration
{

    public function up()
    {

        $this->table('risk_appetite_thresholds')
            ->addForeignKey(
                'risk_classification_id',
                'risk_classifications',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('risk_appetite_thresholds')
            ->dropForeignKey(
                'risk_classification_id'
            );
    }
}

