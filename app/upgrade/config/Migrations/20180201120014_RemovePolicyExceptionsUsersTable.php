<?php
use Phinx\Migration\AbstractMigration;

class RemovePolicyExceptionsUsersTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Requestor' => [
                    'Requestor' => [
                        'joinTable' => 'policy_exceptions_users',
                        'foreignKey' => 'policy_exception_id',
                        'associationForeignKey' => 'user_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'PolicyException', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');
        $this->dropTable('policy_exceptions_users');
    }

    public function down()
    {
        $this->table('policy_exceptions_users')
            ->addColumn('policy_exception_id', 'integer', [
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
                    'policy_exception_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('policy_exceptions_users')
            ->addForeignKey(
                'policy_exception_id',
                'policy_exceptions',
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

