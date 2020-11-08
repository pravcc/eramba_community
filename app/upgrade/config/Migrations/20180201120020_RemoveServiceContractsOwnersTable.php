<?php
use Phinx\Migration\AbstractMigration;

class RemoveServiceContractsOwnersTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Owner' => [
                    'Owner' => [
                        'joinTable' => 'service_contracts_owners',
                        'foreignKey' => 'service_contract_id',
                        'associationForeignKey' => 'owner_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'ServiceContract', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');
        $this->dropTable('service_contracts_owners');
    }

    public function down()
    {
        $this->table('service_contracts_owners')
            ->addColumn('service_contract_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('owner_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'owner_id',
                ]
            )
            ->addIndex(
                [
                    'service_contract_id',
                ]
            )
            ->create();

        $this->table('service_contracts_owners')
            ->addForeignKey(
                'owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'service_contract_id',
                'service_contracts',
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

