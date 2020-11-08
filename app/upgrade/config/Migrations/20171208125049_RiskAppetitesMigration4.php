<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration4 extends AbstractMigration
{

    public function up()
    {
        $this->table('risk_appetite_thresholds')
            ->dropForeignKey([], 'risk_appetite_thresholds_ibfk_2')
            ->removeIndexByName('idx_risk_classification_id')
            ->update();

        $this->table('risk_appetite_thresholds')
            ->removeColumn('risk_classification_id')
            ->update();

        $this->table('risk_appetite_thresholds_risk_classifications')
            ->addColumn('risk_appetite_threshold_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('risk_classification_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => '1',
                'limit' => 3,
                'null' => false,
            ])
            ->addIndex(
                [
                    'risk_appetite_threshold_id',
                ]
            )
            ->addIndex(
                [
                    'risk_classification_id',
                ]
            )
            ->create();

        $this->table('risk_appetite_thresholds_risk_classifications')
            ->addForeignKey(
                'risk_appetite_threshold_id',
                'risk_appetite_thresholds',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
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
        $this->table('risk_appetite_thresholds_risk_classifications')
            ->dropForeignKey(
                'risk_appetite_threshold_id'
            )
            ->dropForeignKey(
                'risk_classification_id'
            );

        $this->table('risk_appetite_thresholds')
            ->addColumn('risk_classification_id', 'integer', [
                'after' => 'risk_appetite_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'risk_classification_id',
                ],
                [
                    'name' => 'idx_risk_classification_id',
                ]
            )
            ->update();

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

        $this->dropTable('risk_appetite_thresholds_risk_classifications');
    }
}

