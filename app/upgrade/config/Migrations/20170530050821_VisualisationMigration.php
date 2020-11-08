<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Config\ConfigInterface;
use Phinx\Console\PhinxApplication;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\StreamOutput;
use Phinx\Config\Config;
use Phinx\Console\Command\SeedRun;
use Symfony\Component\Console\Command\Command;

class VisualisationMigration extends AbstractMigration
{
    public $defaultStatus = '1';

    public function data($up = true) {
        if ($up === true) {
             $data = [
                [
                    'model' => 'Asset',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'AssetReview',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'Risk',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'ThirdPartyRisk',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'BusinessContinuity',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'RiskReview',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'ThirdPartyRiskReview',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'BusinessContinuityReview',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'SecurityPolicy',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'SecurityPolicyReview',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'SecurityService',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'SecurityServiceAudit',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'SecurityServiceMaintenance',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'ComplianceException',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'ComplianceAudit',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'ComplianceAnalysisFinding',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'ComplianceAuditSetting',
                    'status' => $this->defaultStatus
                ],
                [
                    'model' => 'ComplianceFinding',
                    'status' => $this->defaultStatus
                ],
            ];

            $table = $this->table('visualisation_settings');
            $table->insert($data)->saveData();

            $data = [
                [
                    'slug' => 'VISUALISATION',
                    'parent_slug' => 'ACCESSMGT',
                    'name' => 'Visualisation',
                    'icon_code' => NULL,
                    'notes' => NULL,
                    'url' => '{"controller":"visualisationSettings","action":"index", "plugin":"visualisation"}',
                    'hidden' => '0',
                    'order' => '0',
                ]
            ];

            $table = $this->table('setting_groups');
            $table->insert($data)->saveData();
        }

        if ($up !== true) {
            $this->query("DELETE FROM `setting_groups` WHERE (`slug` = 'VISUALISATION')");
        }
    }

    public function up()
    {
        $this->table('custom_roles_role_users')
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
            ->addColumn('field', 'string', [
                'default' => '',
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('custom_roles_role_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'custom_roles_role_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('custom_roles_roles')
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => true,
            ])
            ->addColumn('role', 'string', [
                'default' => '',
                'limit' => 155,
                'null' => false,
            ])
            ->addIndex(
                [
                    'role',
                ]
            )
            ->create();

        $this->table('sections')
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->create();

        $this->table('visualisation_settings')
            ->addColumn('model', 'string', [
                'default' => null,
                'limit' => 155,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => '0',
                'limit' => 3,
                'null' => false,
            ])
            ->create();

        $this->table('visualisation_settings_users')
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
            ->addColumn('user_id', 'integer', [
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
                    'user_id',
                ]
            )
            ->addIndex(
                [
                    'visualisation_setting_id',
                ]
            )
            ->create();

        $this->table('visualisation_share')
            ->addColumn('aros_acos_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
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
                    'user_id',
                ]
            )
            ->create();

        $this->table('custom_roles_role_users')
            ->addForeignKey(
                'custom_roles_role_id',
                'custom_roles_roles',
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

        $this->table('visualisation_settings_users')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
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

        $this->table('visualisation_share')
            ->addForeignKey(
                'aros_acos_id',
                'aros_acos',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
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

        $this->data(true);
    }

    public function down()
    {
        $this->data(false);

        $this->table('custom_roles_role_users')
            ->dropForeignKey(
                'custom_roles_role_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->table('visualisation_settings_users')
            ->dropForeignKey(
                'aros_acos_id'
            )
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'visualisation_setting_id'
            );

        $this->table('visualisation_share')
            ->dropForeignKey(
                'aros_acos_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->dropTable('custom_roles_role_users');

        $this->dropTable('custom_roles_roles');

        $this->dropTable('sections');

        $this->dropTable('visualisation_settings');

        $this->dropTable('visualisation_settings_users');

        $this->dropTable('visualisation_share');
    }
}

