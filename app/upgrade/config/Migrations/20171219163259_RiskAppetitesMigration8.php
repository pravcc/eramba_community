<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration8 extends AbstractMigration
{

    public function up()
    {

        $this->table('risk_appetite_thresholds_risks')
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('risk_appetite_threshold_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'risk_appetite_threshold_id',
                ]
            )
            ->create();

        $this->table('risk_appetite_thresholds_risks')
            ->addForeignKey(
                'risk_appetite_threshold_id',
                'risk_appetite_thresholds',
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
        $this->table('risk_appetite_thresholds_risks')
            ->dropForeignKey(
                'risk_appetite_threshold_id'
            );

        $this->dropTable('risk_appetite_thresholds_risks');
    }
}

