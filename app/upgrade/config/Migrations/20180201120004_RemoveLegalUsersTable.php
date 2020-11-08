<?php
use Phinx\Migration\AbstractMigration;

class RemoveLegalUsersTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'LegalAdvisor' => [
                    'LegalAdvisor' => [
                        'joinTable' => 'legals_users',
                        'foreignKey' => 'legal_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'Legal', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->dropTable('legals_users');
    }

    public function down()
    {
        $this->table('legals_users')
            ->addColumn('legal_id', 'integer', [
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
                    'legal_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('legals_users')
            ->addForeignKey(
                'legal_id',
                'legals',
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

