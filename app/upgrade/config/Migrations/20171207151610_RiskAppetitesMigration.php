<?php
use Phinx\Migration\AbstractMigration;

class RiskAppetitesMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('risk_appetite_thresholds')
            ->addColumn('risk_appetite_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('risk_classification_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('color', 'integer', [
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
                    'risk_appetite_id',
                ]
            )
            ->addIndex(
                [
                    'risk_classification_id',
                ]
            )
            ->create();

        $this->table('risk_appetites')
            ->addColumn('method', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('risk_appetites_risk_classification_types')
            ->addColumn('risk_appetite_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('risk_classification_type_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'risk_appetite_id',
                ]
            )
            ->addIndex(
                [
                    'risk_classification_type_id',
                ]
            )
            ->create();

        $this->table('risk_appetite_thresholds')
            ->addForeignKey(
                'risk_appetite_id',
                'risk_appetites',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('risk_appetites_risk_classification_types')
            ->addForeignKey(
                'risk_appetite_id',
                'risk_appetites',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'risk_classification_type_id',
                'risk_classification_types',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

            $this->insertData();
    }

    public function insertData() {
        $data = [
            [
                'method' => '0', // integer type
                'modified' => date('Y-m-d H:i:s')
            ]
        ];

        $table = $this->table('risk_appetites');
        $table->insert($data)->saveData();
    }

    public function down()
    {
        $this->table('risk_appetite_thresholds')
            ->dropForeignKey(
                'risk_appetite_id'
            );

        $this->table('risk_appetites_risk_classification_types')
            ->dropForeignKey(
                'risk_appetite_id'
            )
            ->dropForeignKey(
                'risk_classification_type_id'
            );

        $this->dropTable('risk_appetite_thresholds');

        $this->dropTable('risk_appetites');

        $this->dropTable('risk_appetites_risk_classification_types');
    }
}

