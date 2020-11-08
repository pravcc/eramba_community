<?php
use Phinx\Migration\AbstractMigration;

class DataAssets extends AbstractMigration
{

    public function up()
    {
        // delete data from data_assets table to not cause foreign key constraint error when migrating to the new version
        $this->query("DELETE FROM `data_assets` WHERE 1=1");
        $this->query("DELETE FROM `data_assets_projects` WHERE 1=1");
        $this->query("DELETE FROM `data_assets_security_services` WHERE 1=1");
        $this->query("DELETE FROM `data_assets_third_parties` WHERE 1=1");
        $this->query("DELETE FROM `business_units_data_assets` WHERE 1=1");

        $this->table('data_assets')
            ->dropForeignKey([], 'data_assets_ibfk_1')
            ->removeIndexByName('asset_id')
            ->update();

        $this->table('data_assets')
            ->removeColumn('asset_id')
            ->update();

        $this->table('data_assets_projects')
            ->removeColumn('created')
            ->update();

        $this->table('data_asset_gdpr')
            ->addColumn('data_asset_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('data_subject', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('volume', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('recived_data', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('contracts', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('retention', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('encryption', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('right_to_erasure', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('origin', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('destination', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('security', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('right_to_portability', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('stakeholders', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('accuracy', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('right_to_access', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('right_to_rectification', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('right_to_decision', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('right_to_restrict', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('right_to_object', 'text', [
                'default' => null,
                'limit' => null,
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
                    'data_asset_id',
                ]
            )
            ->create();

        $this->table('data_asset_gdpr_collection_methods')
            ->addColumn('data_asset_gdpr_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('collection_method', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_gdpr_id',
                ]
            )
            ->create();

        $this->table('data_asset_gdpr_data_types')
            ->addColumn('data_asset_gdpr_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('data_type', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_gdpr_id',
                ]
            )
            ->create();

        $this->table('data_asset_gdpr_lawful_bases')
            ->addColumn('data_asset_gdpr_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('lawful_base', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_gdpr_id',
                ]
            )
            ->create();

        $this->table('data_asset_gdpr_third_party_countries')
            ->addColumn('data_asset_gdpr_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('third_party_country', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_gdpr_id',
                ]
            )
            ->create();

        $this->table('data_asset_instances')
            ->addColumn('asset_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('analysis_unlocked', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->addIndex(
                [
                    'asset_id',
                ]
            )
            ->create();

        $this->table('data_asset_settings')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data_asset_instance_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('gdpr_enabled', 'integer', [
                'default' => null,
                'limit' => 1,
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
                    'data_asset_instance_id',
                ]
            )
            ->create();

        $this->table('data_asset_settings_countries')
            ->addColumn('type', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('data_asset_setting_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('country_id', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_setting_id',
                ]
            )
            ->create();

        $this->table('data_asset_settings_users')
            ->addColumn('type', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('data_asset_setting_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_setting_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('data_assets_risks')
            ->addColumn('model', 'string', [
                'default' => '0',
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('data_asset_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('risk_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_id',
                ]
            )
            ->addIndex(
                [
                    'risk_id',
                ]
            )
            ->create();

        $this->table('data_assets_security_policies')
            ->addColumn('data_asset_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('security_policy_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_id',
                ]
            )
            ->addIndex(
                [
                    'security_policy_id',
                ]
            )
            ->create();

        $this->table('data_asset_gdpr')
            ->addForeignKey(
                'data_asset_id',
                'data_assets',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_gdpr_collection_methods')
            ->addForeignKey(
                'data_asset_gdpr_id',
                'data_asset_gdpr',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_gdpr_data_types')
            ->addForeignKey(
                'data_asset_gdpr_id',
                'data_asset_gdpr',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_gdpr_lawful_bases')
            ->addForeignKey(
                'data_asset_gdpr_id',
                'data_asset_gdpr',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_gdpr_third_party_countries')
            ->addForeignKey(
                'data_asset_gdpr_id',
                'data_asset_gdpr',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_instances')
            ->addForeignKey(
                'asset_id',
                'assets',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_settings')
            ->addForeignKey(
                'data_asset_instance_id',
                'data_asset_instances',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_settings_countries')
            ->addForeignKey(
                'data_asset_setting_id',
                'data_asset_settings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_asset_settings_users')
            ->addForeignKey(
                'data_asset_setting_id',
                'data_asset_settings',
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

        $this->table('data_assets_risks')
            ->addForeignKey(
                'data_asset_id',
                'data_assets',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_assets_security_policies')
            ->addForeignKey(
                'data_asset_id',
                'data_assets',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'security_policy_id',
                'security_policies',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_assets')
            ->addColumn('title', 'string', [
                'after' => 'id',
                'default' => null,
                'length' => 255,
                'null' => false,
            ])
            ->addColumn('data_asset_instance_id', 'integer', [
                'after' => 'description',
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('order', 'integer', [
                'after' => 'data_asset_instance_id',
                'default' => '0',
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'data_asset_instance_id',
                ],
                [
                    'name' => 'FK_data_assets_data_asset_instances',
                ]
            )
            ->addIndex(
                [
                    'order',
                ],
                [
                    'name' => 'FK_data_assets_data_assets',
                ]
            )
            ->update();

        $this->table('data_assets_security_services')
            ->addIndex(
                [
                    'data_asset_id',
                ],
                [
                    'name' => 'data_asset_id',
                ]
            )
            ->addIndex(
                [
                    'security_service_id',
                ],
                [
                    'name' => 'security_service_id',
                ]
            )
            ->update();

        $this->table('data_assets_third_parties')
            ->addIndex(
                [
                    'data_asset_id',
                ],
                [
                    'name' => 'data_asset_id',
                ]
            )
            ->addIndex(
                [
                    'third_party_id',
                ],
                [
                    'name' => 'third_party_id',
                ]
            )
            ->update();

        $this->table('data_assets')
            ->addForeignKey(
                'data_asset_instance_id',
                'data_asset_instances',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_assets_security_services')
            ->addForeignKey(
                'data_asset_id',
                'data_assets',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'security_service_id',
                'security_services',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('data_assets_third_parties')
            ->addForeignKey(
                'data_asset_id',
                'data_assets',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'third_party_id',
                'third_parties',
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
        $this->table('data_asset_gdpr')
            ->dropForeignKey(
                'data_asset_id'
            );

        $this->table('data_asset_gdpr_collection_methods')
            ->dropForeignKey(
                'data_asset_gdpr_id'
            );

        $this->table('data_asset_gdpr_data_types')
            ->dropForeignKey(
                'data_asset_gdpr_id'
            );

        $this->table('data_asset_gdpr_lawful_bases')
            ->dropForeignKey(
                'data_asset_gdpr_id'
            );

        $this->table('data_asset_gdpr_third_party_countries')
            ->dropForeignKey(
                'data_asset_gdpr_id'
            );

        $this->table('data_asset_instances')
            ->dropForeignKey(
                'asset_id'
            );

        $this->table('data_asset_settings')
            ->dropForeignKey(
                'data_asset_instance_id'
            );

        $this->table('data_asset_settings_countries')
            ->dropForeignKey(
                'data_asset_setting_id'
            );

        $this->table('data_asset_settings_users')
            ->dropForeignKey(
                'data_asset_setting_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->table('data_assets_risks')
            ->dropForeignKey(
                'data_asset_id'
            );

        $this->table('data_assets_security_policies')
            ->dropForeignKey(
                'data_asset_id'
            )
            ->dropForeignKey(
                'security_policy_id'
            );

        $this->table('data_assets')
            ->dropForeignKey(
                'data_asset_instance_id'
            );

        $this->table('data_assets_security_services')
            ->dropForeignKey(
                'data_asset_id'
            )
            ->dropForeignKey(
                'security_service_id'
            );

        $this->table('data_assets_third_parties')
            ->dropForeignKey(
                'data_asset_id'
            )
            ->dropForeignKey(
                'third_party_id'
            );

        $this->table('data_assets')
            ->removeIndexByName('FK_data_assets_data_asset_instances')
            ->removeIndexByName('FK_data_assets_data_assets')
            ->update();

        $this->table('data_assets')
            ->addColumn('asset_id', 'integer', [
                'after' => 'data_asset_status_id',
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->removeColumn('title')
            ->removeColumn('data_asset_instance_id')
            ->removeColumn('order')
            ->addIndex(
                [
                    'asset_id',
                ],
                [
                    'name' => 'asset_id',
                ]
            )
            ->update();

        $this->table('data_assets_security_services')
            ->removeIndexByName('data_asset_id')
            ->removeIndexByName('security_service_id')
            ->update();

        $this->table('data_assets_third_parties')
            ->removeIndexByName('data_asset_id')
            ->removeIndexByName('third_party_id')
            ->update();

        $this->table('data_assets_projects')
            ->addColumn('created', 'datetime', [
                'after' => 'data_asset_id',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();

        $this->table('data_assets')
            ->addForeignKey(
                'asset_id',
                'assets',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->dropTable('data_asset_gdpr');

        $this->dropTable('data_asset_gdpr_collection_methods');

        $this->dropTable('data_asset_gdpr_data_types');

        $this->dropTable('data_asset_gdpr_lawful_bases');

        $this->dropTable('data_asset_gdpr_third_party_countries');

        $this->dropTable('data_asset_instances');

        $this->dropTable('data_asset_settings');

        $this->dropTable('data_asset_settings_countries');

        $this->dropTable('data_asset_settings_users');

        $this->dropTable('data_assets_risks');

        $this->dropTable('data_assets_security_policies');
    }
}

