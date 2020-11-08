<?php
use Phinx\Migration\AbstractMigration;

class RemoveSecurityServicesUsersTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Collaborator' => [
                    'Collaborator' => [
                        'joinTable' => 'security_services_users',
                        'foreignKey' => 'security_service_id',
                        'associationForeignKey' => 'user_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'SecurityService', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->dropTable('security_services_users');
    }

    public function down()
    {
        $this->table('security_services_users')
            ->addColumn('security_service_id', 'integer', [
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
                    'security_service_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('security_services_users')
            ->addForeignKey(
                'security_service_id',
                'security_services',
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

