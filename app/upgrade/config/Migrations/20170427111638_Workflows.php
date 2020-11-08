<?php
use Phinx\Migration\AbstractMigration;

class Workflows extends AbstractMigration
{

    public function up()
    {

        $this->table('wf_access_models')
            ->addColumn('plugin', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 155,
                'null' => false,
            ])
            ->addIndex(
                [
                    'name',
                ]
            )
            ->create();

        $this->table('wf_access_types')
            ->addColumn('slug', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => true,
            ])
            ->addIndex(
                [
                    'model',
                ]
            )
            ->addIndex(
                [
                    'slug',
                ]
            )
            ->create();

        $this->table('wf_accesses')
            ->addColumn('wf_access_model', 'string', [
                'default' => '',
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('wf_access_foreign_key', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('wf_access_type', 'string', [
                'default' => '',
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('access', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->addIndex(
                [
                    'wf_access_model',
                ]
            )
            ->addIndex(
                [
                    'wf_access_type',
                ]
            )
            ->create();

        $this->table('wf_instance_approvals')
            ->addColumn('wf_instance_request_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('wf_stage_id', 'integer', [
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
                    'wf_instance_request_id',
                ]
            )
            ->addIndex(
                [
                    'wf_stage_id',
                ]
            )
            ->create();

        $this->table('wf_instance_logs')
            ->addColumn('wf_instance_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('wf_stage_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('message', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
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
                    'user_id',
                ]
            )
            ->addIndex(
                [
                    'wf_instance_id',
                ]
            )
            ->create();

        $this->table('wf_instance_requests')
            ->addColumn('wf_instance_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('wf_stage_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('wf_stage_step_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('status', 'integer', [
                'default' => '1',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('approvals_count', 'integer', [
                'default' => '0',
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
                    'user_id',
                ]
            )
            ->addIndex(
                [
                    'wf_instance_id',
                ]
            )
            ->addIndex(
                [
                    'wf_stage_id',
                ]
            )
            ->addIndex(
                [
                    'wf_stage_step_id',
                ]
            )
            ->create();

        $this->table('wf_instances')
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('wf_stage_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('wf_stage_step_id', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('stage_init_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => '1',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('pending_requests', 'integer', [
                'default' => '0',
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
                    'model',
                ]
            )
            ->addIndex(
                [
                    'wf_stage_id',
                ]
            )
            ->create();

        $this->table('wf_settings')
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'model',
                ]
            )
            ->create();

        $this->table('wf_stage_step_conditions')
            ->addColumn('wf_stage_step_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('field', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('comparison_type', 'string', [
                'default' => null,
                'limit' => 55,
                'null' => false,
            ])
            ->addColumn('value', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => true,
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
                    'wf_stage_step_id',
                ]
            )
            ->create();

        $this->table('wf_stage_steps')
            ->addColumn('wf_stage_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('wf_next_stage_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('step_type', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('notification_message', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('timeout', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
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
                    'wf_next_stage_id',
                ]
            )
            ->addIndex(
                [
                    'wf_stage_id',
                ]
            )
            ->create();

        $this->table('wf_stages')
            ->addColumn('model', 'string', [
                'default' => '',
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('wf_setting_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('stage_type', 'integer', [
                'default' => null,
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('approval_method', 'integer', [
                'default' => null,
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('timeout', 'integer', [
                'default' => null,
                'limit' => 5,
                'null' => true,
            ])
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
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
                    'model',
                ]
            )
            ->addIndex(
                [
                    'wf_setting_id',
                ]
            )
            ->create();

        $this->table('wf_accesses')
            ->addForeignKey(
                'wf_access_model',
                'wf_access_models',
                'name',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'wf_access_type',
                'wf_access_types',
                'slug',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('wf_instance_approvals')
            ->addForeignKey(
                'wf_instance_request_id',
                'wf_instance_requests',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'wf_stage_id',
                'wf_stages',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('wf_instance_logs')
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
                'wf_instance_id',
                'wf_instances',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('wf_instance_requests')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->addForeignKey(
                'wf_instance_id',
                'wf_instances',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'wf_stage_id',
                'wf_stages',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'wf_stage_step_id',
                'wf_stage_steps',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('wf_instances')
            ->addForeignKey(
                'model',
                'wf_settings',
                'model',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'wf_stage_id',
                'wf_stages',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('wf_stage_step_conditions')
            ->addForeignKey(
                'wf_stage_step_id',
                'wf_stage_steps',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('wf_stage_steps')
            ->addForeignKey(
                'wf_next_stage_id',
                'wf_stages',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'wf_stage_id',
                'wf_stages',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('wf_stages')
            ->addForeignKey(
                'model',
                'wf_settings',
                'model',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'wf_setting_id',
                'wf_settings',
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
        $this->table('wf_accesses')
            ->dropForeignKey(
                'wf_access_model'
            )
            ->dropForeignKey(
                'wf_access_type'
            );

        $this->table('wf_instance_approvals')
            ->dropForeignKey(
                'wf_instance_request_id'
            )
            ->dropForeignKey(
                'wf_stage_id'
            );

        $this->table('wf_instance_logs')
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'wf_instance_id'
            );

        $this->table('wf_instance_requests')
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'wf_instance_id'
            )
            ->dropForeignKey(
                'wf_stage_id'
            )
            ->dropForeignKey(
                'wf_stage_step_id'
            );

        $this->table('wf_instances')
            ->dropForeignKey(
                'model'
            )
            ->dropForeignKey(
                'wf_stage_id'
            );

        $this->table('wf_stage_step_conditions')
            ->dropForeignKey(
                'wf_stage_step_id'
            );

        $this->table('wf_stage_steps')
            ->dropForeignKey(
                'wf_next_stage_id'
            )
            ->dropForeignKey(
                'wf_stage_id'
            );

        $this->table('wf_stages')
            ->dropForeignKey(
                'model'
            )
            ->dropForeignKey(
                'wf_setting_id'
            );

        $this->dropTable('wf_access_models');

        $this->dropTable('wf_access_types');

        $this->dropTable('wf_accesses');

        $this->dropTable('wf_instance_approvals');

        $this->dropTable('wf_instance_logs');

        $this->dropTable('wf_instance_requests');

        $this->dropTable('wf_instances');

        $this->dropTable('wf_settings');

        $this->dropTable('wf_stage_step_conditions');

        $this->dropTable('wf_stage_steps');

        $this->dropTable('wf_stages');
    }
}

