<?php
use Phinx\Migration\AbstractMigration;

class DataAssetInstancesObjectStatus extends AbstractMigration
{

    public function up()
    {

        $this->table('data_assets')
            ->removeColumn('incomplete_analysis')
            ->removeColumn('incomplete_gdpr_analysis')
            ->update();

        $this->table('data_asset_instances')
            ->addColumn('asset_missing_review', 'integer', [
                'after' => 'analysis_unlocked',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('controls_with_issues', 'integer', [
                'after' => 'asset_missing_review',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('controls_with_failed_audits', 'integer', [
                'after' => 'controls_with_issues',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('policies_with_missing_reviews', 'integer', [
                'after' => 'controls_with_failed_audits',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('risks_with_missing_reviews', 'integer', [
                'after' => 'policies_with_missing_reviews',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('project_expired', 'integer', [
                'after' => 'risks_with_missing_reviews',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('expired_tasks', 'integer', [
                'after' => 'project_expired',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('incomplete_analysis', 'integer', [
                'after' => 'expired_tasks',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('incomplete_gdpr_analysis', 'integer', [
                'after' => 'incomplete_analysis',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'after' => 'incomplete_gdpr_analysis',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'after' => 'created',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('data_asset_instances')
            ->removeColumn('asset_missing_review')
            ->removeColumn('controls_with_issues')
            ->removeColumn('controls_with_failed_audits')
            ->removeColumn('policies_with_missing_reviews')
            ->removeColumn('risks_with_missing_reviews')
            ->removeColumn('project_expired')
            ->removeColumn('expired_tasks')
            ->removeColumn('incomplete_analysis')
            ->removeColumn('incomplete_gdpr_analysis')
            ->removeColumn('created')
            ->removeColumn('modified')
            ->update();

        $this->table('data_assets')
            ->addColumn('incomplete_analysis', 'integer', [
                'after' => 'order',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->addColumn('incomplete_gdpr_analysis', 'integer', [
                'after' => 'incomplete_analysis',
                'default' => '0',
                'length' => 3,
                'null' => false,
            ])
            ->update();
    }
}

