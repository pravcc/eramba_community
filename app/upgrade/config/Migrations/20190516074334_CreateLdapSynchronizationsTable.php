<?php
use Phinx\Migration\AbstractMigration;

class CreateLdapSynchronizationsTable extends AbstractMigration
{
    public function up()
    {
       $this->createLdapSyncTable();
       $this->createLdapSynchronizationsGroupsTable();
       $this->createLdapSynchronizationsPortalsTable();
    }

    private function createLdapSyncTable()
    {
         $this->table('ldap_synchronizations')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('ldap_auth_connector_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('ldap_group_connector_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('ldap_group', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false
            ])
            ->addColumn('status', 'integer', [
                'default' => 1,
                'limit' => 1,
                'null' => false
            ])
            ->addColumn('language', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => false
            ])
            ->addColumn('api', 'integer', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('no_user_action', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'ldap_auth_connector_id',
                ]
            )
            ->addIndex(
                [
                    'ldap_group_connector_id',
                ]
            )
            ->create();

        $this->table('ldap_synchronizations')
            ->addForeignKey(
                'ldap_auth_connector_id',
                'ldap_connectors',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'ldap_group_connector_id',
                'ldap_connectors',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    private function createLdapSynchronizationsPortalsTable()
    {
        $this->table('ldap_synchronizations_portals')
            ->addColumn('ldap_synchronization_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
            ])
            ->addColumn('portal_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'ldap_synchronization_id'
                ]
            )
            ->addIndex(
                [
                    'portal_id'
                ]
            )
            ->create();

        $this->table('ldap_synchronizations_portals')
            ->addForeignKey(
                'ldap_synchronization_id',
                'ldap_synchronizations',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'portal_id',
                'portals',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    private function createLdapSynchronizationsGroupsTable()
    {
        $this->table('ldap_synchronizations_groups')
            ->addColumn('ldap_synchronization_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
            ])
            ->addColumn('group_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'ldap_synchronization_id'
                ]
            )
            ->addIndex(
                [
                    'group_id'
                ]
            )
            ->create();

        $this->table('ldap_synchronizations_groups')
            ->addForeignKey(
                'ldap_synchronization_id',
                'ldap_synchronizations',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'group_id',
                'groups',
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
        $this->deleteLdapSynchronizationsGroupsTable();
        $this->deleteLdapSynchronizationsPortalsTable();
        $this->deleteLdapSyncTable();
    }

    private function deleteLdapSyncTable()
    {
        $this->table('ldap_synchronizations')
            ->dropForeignKey(
                'ldap_auth_connector_id'
            )
            ->dropForeignKey(
                'ldap_group_connector_id'
            );

        $this->dropTable('ldap_synchronizations');
    }

    private function deleteLdapSynchronizationsGroupsTable()
    {
        $this->table('ldap_synchronizations_groups')
            ->dropForeignKey(
                'ldap_synchronization_id'
            )
            ->dropForeignKey(
                'group_id'
            );

        $this->dropTable('ldap_synchronizations_groups');
    }

    private function deleteLdapSynchronizationsPortalsTable()
    {
        $this->table('ldap_synchronizations_portals')
            ->dropForeignKey(
                'ldap_synchronization_id'
            )
            ->dropForeignKey(
                'portal_id'
            );

        $this->dropTable('ldap_synchronizations_portals');
    }
}
