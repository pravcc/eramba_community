<?php
use Phinx\Migration\AbstractMigration;

class AccountReviewFindingsMigration extends AbstractMigration
{
    public $defaultVisualisationStatus = '1';
    
    public function up()
    {

        $this->table('account_review_findings')
            ->addColumn('account_review_pull_id', 'integer', [
                'default' => null,
                'limit' => 11,
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
            ->addColumn('deadline', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('close_date', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('auto_close_date', 'integer', [
                'default' => '1',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('expired', 'integer', [
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
                    'account_review_pull_id',
                ]
            )
            ->create();

        $this->table('account_review_findings_feedbacks')
            ->addColumn('account_review_finding_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('account_review_feedback_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'account_review_feedback_id',
                ]
            )
            ->addIndex(
                [
                    'account_review_finding_id',
                ]
            )
            ->create();

        $this->table('account_review_findings_users')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('account_review_finding_id', 'integer', [
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
                    'account_review_finding_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('account_review_findings')
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

        $this->table('account_review_findings_feedbacks')
            ->addForeignKey(
                'account_review_feedback_id',
                'account_review_feedbacks',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'account_review_finding_id',
                'account_review_findings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('account_review_findings_users')
            ->addForeignKey(
                'account_review_finding_id',
                'account_review_findings',
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

        $visualisationData = [
            [
                'model' => 'AccountReviewFinding',
                'status' => $this->defaultVisualisationStatus
            ],
        ];
        $this->table('visualisation_settings')
            ->insert($visualisationData)
            ->saveData();
    }

    public function down()
    {
        $this->table('account_review_findings')
            ->dropForeignKey(
                'account_review_pull_id'
            );

        $this->table('account_review_findings_feedbacks')
            ->dropForeignKey(
                'account_review_feedback_id'
            )
            ->dropForeignKey(
                'account_review_finding_id'
            );

        $this->table('account_review_findings_users')
            ->dropForeignKey(
                'account_review_finding_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->dropTable('account_review_findings');

        $this->dropTable('account_review_findings_feedbacks');

        $this->dropTable('account_review_findings_users');
    }
}

