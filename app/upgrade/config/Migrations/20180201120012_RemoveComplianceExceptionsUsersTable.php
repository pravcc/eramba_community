<?php
use Phinx\Migration\AbstractMigration;

class RemoveComplianceExceptionsUsersTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Requestor' => [
                    'Requestor' => [
                        'joinTable' => 'compliance_exceptions_users',
                        'foreignKey' => 'compliance_exception_id',
                        'associationForeignKey' => 'user_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'ComplianceException', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');
        $this->dropTable('compliance_exceptions_users');
    }

    public function down()
    {
        $this->table('compliance_exceptions_users')
            ->addColumn('compliance_exception_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'compliance_exception_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('compliance_exceptions_users')
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
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

