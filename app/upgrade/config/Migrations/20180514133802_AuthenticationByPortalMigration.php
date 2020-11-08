<?php
use Phinx\Migration\AbstractMigration;

class AuthenticationByPortalMigration extends AbstractMigration
{
    protected function setAccessToMainPortal()
    {
        if (class_exists('App'))
        {
            App::uses('ClassRegistry', 'Utility');
            ClassRegistry::flush();

            $cacheGlobalConfig = Configure::read('Cache.disable');
            Configure::write('Cache.disable', true);
            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;

            ClassRegistry::init('UsersPortal')->setAccessToMainPortalForAllUsers();
        }
    }

    public function up()
    {
        $this->table('portals')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('controller', 'string', [
                'default' => null,
                'limit' => 255,
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
            ->create();

        // Insert portals
        $rows = [
            [
                'id'    => 1,
                'name'  => 'main',
                'controller' => 'users'
            ],
            [
                'id'    => 2,
                'name'  => 'vendor_assessments',
                'controller' => 'vendorAssessmentFeedbacks'
            ],
            [
                'id'    => 3,
                'name'  => 'account_reviews',
                'controller' => 'accountReviewPortal'
            ]
        ];

        $table = $this->table('portals');
        $table->insert($rows);
        $table->saveData();

        $this->table('users_portals')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('portal_id', 'integer', [
                'default' => null,
                'limit' => 11,
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
                    'portal_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('users_portals')
            ->addForeignKey(
                'portal_id',
                'portals',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->setAccessToMainPortal();
    }

    public function down()
    {
        $this->table('users_portals')
            ->dropForeignKey(
                'portal_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->dropTable('portals');

        $this->dropTable('users_portals');
    }
}

