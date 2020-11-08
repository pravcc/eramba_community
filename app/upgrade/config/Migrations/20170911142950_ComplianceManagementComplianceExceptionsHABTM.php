<?php
use Phinx\Migration\AbstractMigration;

class ComplianceManagementComplianceExceptionsHABTM extends AbstractMigration
{

    public $autoId = false;

    public function up()
    {
        $this->table('compliance_managements')
            ->dropForeignKey([], 'compliance_managements_ibfk_3')
            ->removeIndexByName('compliance_exception_id')
            ->update();

        $this->table('compliance_managements')
            ->removeColumn('compliance_exception_id')
            ->update();

        $this->table('compliance_exceptions_compliance_managements')
            ->addColumn('compliance_exception_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('compliance_management_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'compliance_exception_id',
                ]
            )
            ->addIndex(
                [
                    'compliance_management_id',
                ]
            )
            ->create();

        $this->table('compliance_exceptions_compliance_managements')
            ->addForeignKey(
                'compliance_exception_id',
                'compliance_exceptions',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'compliance_management_id',
                'compliance_managements',
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
        $this->table('compliance_exceptions_compliance_managements')
            ->dropForeignKey(
                'compliance_exception_id'
            )
            ->dropForeignKey(
                'compliance_management_id'
            );

        $this->table('compliance_managements')
            ->addColumn('compliance_exception_id', 'integer', [
                'after' => 'compliance_treatment_strategy_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'compliance_exception_id',
                ],
                [
                    'name' => 'compliance_exception_id',
                ]
            )
            ->update();

        $this->table('compliance_managements')
            ->addForeignKey(
                'compliance_exception_id',
                'compliance_exceptions',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->update();

        $this->dropTable('compliance_exceptions_compliance_managements');
    }
}

