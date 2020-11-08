<?php
use Phinx\Migration\AbstractMigration;

class RemoveThirdPartiesUsersTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Sponsor' => [
                    'Sponsor' => [
                        'joinTable' => 'third_parties_users',
                        'foreignKey' => 'third_party_id',
                        'associationForeignKey' => 'user_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'ThirdParty', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');
        $this->dropTable('third_parties_users');
    }

    public function down()
    {
        $this->table('third_parties_users')
            ->addColumn('third_party_id', 'integer', [
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
                    'third_party_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('third_parties_users')
            ->addForeignKey(
                'third_party_id',
                'third_parties',
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

