<?php
use Phinx\Migration\AbstractMigration;

class RemoveAuthorAndCollaboratosFromSecurityPolicies extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Owner' => 'author_id',
                'Collaborator' => [
                    'Collaborator' => [
                        'joinTable' => 'security_policies_users',
                        'foreignKey' => 'security_policy_id',
                        'associationForeignKey' => 'user_id'
                    ]
                ]
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'SecurityPolicy', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('security_policies')
            ->dropForeignKey([], 'security_policies_ibfk_2')
            ->removeIndexByName('author_id')
            ->update();

        $this->table('security_policies')
            ->removeColumn('author_id')
            ->update();

        $this->dropTable('security_policies_users');
    }

    public function down()
    {

        $this->table('security_policies_users')
            ->addColumn('security_policy_id', 'integer', [
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
                    'security_policy_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('security_policies_users')
            ->addForeignKey(
                'security_policy_id',
                'security_policies',
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

        $this->table('security_policies')
            ->addColumn('author_id', 'integer', [
                'after' => 'expired_reviews',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'author_id',
                ],
                [
                    'name' => 'author_id',
                ]
            )
            ->update();

        $this->table('security_policies')
            ->addForeignKey(
                'author_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'security_policies_ibfk_2'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

