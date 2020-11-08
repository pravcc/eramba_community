<?php
use Phinx\Migration\AbstractMigration;

class AddUsersLdapSynchronizationsTable extends AbstractMigration
{
    public function up()
    {
        $this->table('users_ldap_synchronizations')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
            ])
            ->addColumn('ldap_synchronization_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->create();

        $this->table('users_ldap_synchronizations')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'ldap_synchronization_id',
                'ldap_synchronizations',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        if (class_exists('App')) {
            $User = ClassRegistry::init('User');
            $ldapList = $User->find('list', [
                'fields' => [
                    'id', 'ldap_synchronization_id'
                ],
                'order' => [
                    'id' => 'ASC'
                ],
                'recursive' => -1
            ]);

            $rows = [];
            foreach ($ldapList as $userId => $syncId) {
                if ($syncId != null) {
                    $rows[] = [
                        'user_id' => $userId,
                        'ldap_synchronization_id' => $syncId
                    ];
                }
            }

            if (!empty($rows)) {
                $table = $this->table('users_ldap_synchronizations');
                $table->insert($rows);
                $table->saveData();
            }
        }
    }

    public function down()
    {
        $this->table('users_ldap_synchronizations')
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'ldap_synchronization_id'
            );

        $this->dropTable('users_ldap_synchronizations');
    }
}
