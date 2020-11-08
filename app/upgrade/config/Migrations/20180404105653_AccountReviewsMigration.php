<?php
use Phinx\Migration\AbstractMigration;

class AccountReviewsMigration extends AbstractMigration
{
    public $defaultVisualisationStatus = '1';

    public function up()
    {

        $this->table('account_review_feed_pulls')
            ->addColumn('account_review_feed_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('account_review_pull_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
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
            ->addColumn('deleted', 'integer', [
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'account_review_feed_id',
                ]
            )
            ->addIndex(
                [
                    'account_review_pull_id',
                ]
            )
            ->create();

        $this->table('account_review_feed_row_roles')
            ->addColumn('account_review_feed_row_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'account_review_feed_row_id',
                ]
            )
            ->create();

        $this->table('account_review_feed_rows')
            ->addColumn('account_review_feed_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('account_review_feed_pull_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'account_review_feed_id',
                ]
            )
            ->addIndex(
                [
                    'account_review_feed_pull_id',
                ]
            )
            ->create();

        $this->table('account_review_feedback_roles')
            ->addColumn('account_review_feedback_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'account_review_feedback_id',
                ]
            )
            ->create();

        $this->table('account_review_feedbacks')
            ->addColumn('account_review_pull_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('account_review_feed_row_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('answer', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('locked', 'integer', [
                'default' => '0',
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
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('deleted', 'integer', [
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'account_review_feed_row_id',
                ]
            )
            ->addIndex(
                [
                    'account_review_pull_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('account_review_feeds')
            ->addColumn('title', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('path', 'text', [
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
            ->addColumn('deleted', 'integer', [
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('account_review_pulls')
            ->addColumn('hash', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('account_review_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('submitted', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('submit_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('count_check', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('count_added', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('count_deleted', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('count_current_check', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('count_former_check', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('count_role_change', 'integer', [
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
            ->addColumn('deleted', 'integer', [
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'account_review_id',
                ]
            )
            ->create();

        $this->table('account_reviews')
            ->addColumn('hash', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('frequency', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('frequency_type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('comparison_type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('account_review_feed_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('comparison_account_review_feed_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('portal_title', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('portal_description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('incomplete_submit', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('auto_submit_empty', 'integer', [
                'default' => '1',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
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
            ->addColumn('deleted', 'integer', [
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
             ->addIndex(
                [
                    'account_review_feed_id',
                ]
            )
            ->addIndex(
                [
                    'comparison_account_review_feed_id',
                ]
            )
            ->create();

        $this->table('account_reviews_assets')
            ->addColumn('account_review_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('asset_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'account_review_id',
                ]
            )
            ->addIndex(
                [
                    'asset_id',
                ]
            )
            ->create();

        $this->table('account_reviews_users')
            ->addColumn('account_review_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'account_review_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('account_review_feed_pulls')
            ->addForeignKey(
                'account_review_feed_id',
                'account_review_feeds',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'account_review_pull_id',
                'account_review_pulls',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('account_review_feed_row_roles')
            ->addForeignKey(
                'account_review_feed_row_id',
                'account_review_feed_rows',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('account_review_feed_rows')
            ->addForeignKey(
                'account_review_feed_id',
                'account_review_feeds',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'account_review_feed_pull_id',
                'account_review_feed_pulls',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('account_review_feedback_roles')
            ->addForeignKey(
                'account_review_feedback_id',
                'account_review_feedbacks',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('account_review_feedbacks')
            ->addForeignKey(
                'account_review_feed_row_id',
                'account_review_feed_rows',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'account_review_pull_id',
                'account_review_pulls',
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
                    'delete' => 'SET_NULL'
                ]
            )
            ->update();

        $this->table('account_review_pulls')
            ->addForeignKey(
                'account_review_id',
                'account_reviews',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('account_reviews')
            ->addForeignKey(
                'account_review_feed_id',
                'account_review_feeds',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->addForeignKey(
                'comparison_account_review_feed_id',
                'account_review_feeds',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->update();

        $this->table('account_reviews_assets')
            ->addForeignKey(
                'account_review_id',
                'account_reviews',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
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

        $this->table('account_reviews_users')
            ->addForeignKey(
                'account_review_id',
                'account_reviews',
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

        $this->table('ldap_connector_authentication')
            ->addColumn('auth_account_review', 'integer', [
                'after' => 'auth_vendor_assessment',
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->update();

        $visualisationData = [
            [
                'model' => 'AccountReview',
                'status' => $this->defaultVisualisationStatus
            ],
            [
                'model' => 'AccountReviewPull',
                'status' => $this->defaultVisualisationStatus
            ],
            [
                'model' => 'AccountReviewFeedback',
                'status' => $this->defaultVisualisationStatus
            ],
        ];
        $this->table('visualisation_settings')
            ->insert($visualisationData)
            ->saveData();
    }

    public function down()
    {
        $this->table('account_review_feed_pulls')
            ->dropForeignKey(
                'account_review_feed_id'
            )
            ->dropForeignKey(
                'account_review_pull_id'
            );

        $this->table('account_review_feed_row_roles')
            ->dropForeignKey(
                'account_review_feed_row_id'
            );

        $this->table('account_review_feed_rows')
            ->dropForeignKey(
                'account_review_feed_id'
            )
            ->dropForeignKey(
                'account_review_feed_pull_id'
            );

        $this->table('account_review_feedback_roles')
            ->dropForeignKey(
                'account_review_feedback_id'
            );

        $this->table('account_review_feedbacks')
            ->dropForeignKey(
                'account_review_feed_row_id'
            )
            ->dropForeignKey(
                'account_review_pull_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->table('account_review_pulls')
            ->dropForeignKey(
                'account_review_id'
            );

        $this->table('account_reviews')
            ->dropForeignKey(
                'account_review_feed_id'
            )
            ->dropForeignKey(
                'comparison_account_review_feed_id'
            );

        $this->table('account_reviews_assets')
            ->dropForeignKey(
                'account_review_id'
            )
            ->dropForeignKey(
                'asset_id'
            );

        $this->table('account_reviews_users')
            ->dropForeignKey(
                'account_review_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->table('ldap_connector_authentication')
            ->removeColumn('auth_account_review')
            ->update();

        $this->dropTable('account_review_feed_pulls');

        $this->dropTable('account_review_feed_row_roles');

        $this->dropTable('account_review_feed_rows');

        $this->dropTable('account_review_feedback_roles');

        $this->dropTable('account_review_feedbacks');

        $this->dropTable('account_review_feeds');

        $this->dropTable('account_review_pulls');

        $this->dropTable('account_reviews');

        $this->dropTable('account_reviews_assets');

        $this->dropTable('account_reviews_users');
    }
}

