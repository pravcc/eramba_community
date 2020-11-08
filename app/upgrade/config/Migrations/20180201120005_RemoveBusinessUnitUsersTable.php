<?php
use Phinx\Migration\AbstractMigration;

class RemoveBusinessUnitUsersTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'BusinessUnitOwner' => [
                    'BusinessUnitOwner' => [
                        'joinTable' => 'business_units_users',
                        'foreignKey' => 'business_unit_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'BusinessUnit', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->dropTable('business_units_users');
    }

    public function down()
    {
        $this->table('business_units_users')
            ->addColumn('business_unit_id', 'integer', [
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
                    'business_unit_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('business_units_users')
            ->addForeignKey(
                'business_unit_id',
                'business_units',
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

