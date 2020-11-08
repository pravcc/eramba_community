<?php
use Phinx\Migration\AbstractMigration;

class VisualisationUserFieldsMigration extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'ExemptedUser' => 'user_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'VisualisationSettingsUser', $fields);

            $fields = [
                'SharedUser' => 'user_id'
            ];
            $table = '';
            if ($type == 'up') {
                $table = 'visualisation_share';
            } elseif ($type == 'down') {
                $table = 'share';
            }
            $UserFields->moveExistingFieldsToUserFieldsTable($type, [
                'class' => 'VisualisationShareUser',
                'table' => $table
            ], $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        if (class_exists('App')) {
            $cacheConfig = Configure::write('Cache.disable');
            Configure::write('Cache.disable', true);

            App::uses('Configure', 'Core');
            App::uses('ConnectionManager', 'Model');
            App::uses('ClassRegistry', 'Utility');

            App::uses('AppVisualisationMigration', 'Visualisation.Lib');
            $AppVisualisationMigration = new AppVisualisationMigration();

            $AppVisualisationMigration->beforeSchemaUpdate('up');
        }

        $this->table('visualisation_settings_users')
            ->dropForeignKey([], 'visualisation_settings_users_ibfk_2')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('visualisation_settings_users')
            ->removeColumn('user_id')
            ->update();
        $this->table('visualisation_share')
            ->dropForeignKey([], 'visualisation_share_ibfk_2')
            ->dropForeignKey([], 'visualisation_share_ibfk_3')
            ->removeIndexByName('aros_acos_id')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('visualisation_share')
            ->removeColumn('aros_acos_id')
            ->removeColumn('user_id')
            ->removeColumn('created')
            ->update();

        $this->table('visualisation_settings_groups')
            ->addColumn('visualisation_setting_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('aros_acos_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_fields_group_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'aros_acos_id',
                ]
            )
            ->addIndex(
                [
                    'user_fields_group_id',
                ]
            )
            ->addIndex(
                [
                    'visualisation_setting_id',
                ]
            )
            ->create();

        $this->table('visualisation_share_groups')
            ->addColumn('visualisation_share_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('aros_acos_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_fields_group_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'aros_acos_id',
                ]
            )
            ->addIndex(
                [
                    'user_fields_group_id',
                ]
            )
            ->addIndex(
                [
                    'visualisation_share_id',
                ]
            )
            ->create();

        $this->table('visualisation_share_users')
            ->addColumn('visualisation_share_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('aros_acos_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_fields_user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'aros_acos_id',
                ]
            )
            ->addIndex(
                [
                    'user_fields_user_id',
                ]
            )
            ->addIndex(
                [
                    'visualisation_share_id',
                ]
            )
            ->create();

        $this->table('visualisation_settings_groups')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'user_fields_group_id',
                'user_fields_groups',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'visualisation_setting_id',
                'visualisation_settings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('visualisation_share_groups')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'user_fields_group_id',
                'user_fields_groups',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'visualisation_share_id',
                'visualisation_share',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('visualisation_share_users')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'user_fields_user_id',
                'user_fields_users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'visualisation_share_id',
                'visualisation_share',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('visualisation_settings_users')
            ->addColumn('user_fields_user_id', 'integer', [
                'after' => 'aros_acos_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'user_fields_user_id',
                ],
                [
                    'name' => 'user_fields_user_id',
                ]
            )
            ->update();

        $this->table('visualisation_share')
            ->addColumn('model', 'string', [
                'after' => 'id',
                'default' => null,
                'length' => 128,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'after' => 'model',
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->update();

        $this->table('visualisation_settings_users')
            ->addForeignKey(
                'user_fields_user_id',
                'user_fields_users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        if (class_exists('App')) {
            ClassRegistry::flush();
            
            $Group = ClassRegistry::init('Group');
            $Group->virtualFields['full_name_with_type'] = "CONCAT(`{$Group->alias}`.`name`, ' ', '(" . __('Group') . ")')";

            $User = ClassRegistry::init('User');
            $User->virtualFields['full_name_with_type'] = "CONCAT(`{$User->alias}`.`name`, ' ', `{$User->alias}`.`surname`, ' ', '(" . __('User') . ")')";

            $AppVisualisationMigration->afterSchemaUpdate('up');

            Configure::write('Cache.disable', $cacheConfig);
        }
    }

    public function down()
    {
        if (class_exists('App')) {
            $cacheConfig = Configure::write('Cache.disable');
            Configure::write('Cache.disable', true);

            App::uses('Configure', 'Core');
            App::uses('ConnectionManager', 'Model');
            App::uses('ClassRegistry', 'Utility');

            App::uses('AppVisualisationMigration', 'Visualisation.Lib');
            $AppVisualisationMigration = new AppVisualisationMigration();

            $AppVisualisationMigration->beforeSchemaUpdate('down');
        }

        $this->table('visualisation_settings_groups')
            ->dropForeignKey(
                'aros_acos_id'
            )
            ->dropForeignKey(
                'user_fields_group_id'
            )
            ->dropForeignKey(
                'visualisation_setting_id'
            );

        $this->table('visualisation_share_groups')
            ->dropForeignKey(
                'aros_acos_id'
            )
            ->dropForeignKey(
                'user_fields_group_id'
            )
            ->dropForeignKey(
                'visualisation_share_id'
            );

        $this->table('visualisation_share_users')
            ->dropForeignKey(
                'aros_acos_id'
            )
            ->dropForeignKey(
                'user_fields_user_id'
            )
            ->dropForeignKey(
                'visualisation_share_id'
            );

        $this->table('visualisation_settings_users')
            ->dropForeignKey(
                'user_fields_user_id'
            );

        $this->table('visualisation_settings_users')
            ->removeIndexByName('user_fields_user_id')
            ->update();

        $this->table('visualisation_settings_users')
            ->addColumn('user_id', 'integer', [
                'after' => 'aros_acos_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->removeColumn('user_fields_user_id')
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'user_id',
                ]
            )
            ->update();

        $this->table('visualisation_share')
            ->addColumn('aros_acos_id', 'integer', [
                'after' => 'id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'after' => 'aros_acos_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'after' => 'user_id',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->removeColumn('model')
            ->removeColumn('foreign_key')
            ->addIndex(
                [
                    'aros_acos_id',
                ],
                [
                    'name' => 'aros_acos_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'user_id',
                ]
            )
            ->update();

        $this->table('visualisation_settings_users')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'visualisation_settings_users_ibfk_2'
                ]
            )
            ->update();

        $this->table('visualisation_share')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'visualisation_share_ibfk_3'
                ]
            )
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'visualisation_share_ibfk_2'
                ]
            )
            ->update();

        $this->dropTable('visualisation_settings_groups');

        $this->dropTable('visualisation_share_groups');

        $this->dropTable('visualisation_share_users');

        if (class_exists('App')) {
            $AppVisualisationMigration->afterSchemaUpdate('down');

            Configure::write('Cache.disable', $cacheConfig);
        }

        $this->migrateData('down');
    }
}

