<?php
use Phinx\Migration\AbstractMigration;

class AppNotificationMigration extends AbstractMigration
{

    public function up()
    {
        $this->table('app_notification_params')
            ->addColumn('app_notification_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('key', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('value', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'app_notification_id',
                ]
            )
            ->create();

        $this->table('app_notification_views')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('notifications_view', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('app_notifications')
            ->addColumn('notification', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('expiration', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('app_notification_params')
            ->addForeignKey(
                'app_notification_id',
                'app_notifications',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('app_notification_views')
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

        $data = [
            [
                'model' => 'AppNotification',
                'status' => '1'
            ],
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();
    }

    public function down()
    {
        $this->table('app_notification_params')
            ->dropForeignKey(
                'app_notification_id'
            );

        $this->table('app_notification_views')
            ->dropForeignKey(
                'user_id'
            );

        $this->dropTable('app_notification_params');

        $this->dropTable('app_notification_views');

        $this->dropTable('app_notifications');
    }
}

