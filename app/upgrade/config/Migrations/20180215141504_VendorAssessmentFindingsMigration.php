<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentFindingsMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('users_vendor_assessment_findings')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_finding_id', 'integer', [
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
                    'user_id',
                ]
            )
            ->addIndex(
                [
                    'vendor_assessment_finding_id',
                ]
            )
            ->create();

        $this->table('vendor_assessment_findings')
            ->addColumn('vendor_assessment_id', 'integer', [
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
                    'vendor_assessment_id',
                ]
            )
            ->create();

        $this->table('users_vendor_assessment_findings')
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
                'vendor_assessment_finding_id',
                'vendor_assessment_findings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('vendor_assessment_findings')
            ->addForeignKey(
                'vendor_assessment_id',
                'vendor_assessments',
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
        $this->table('users_vendor_assessment_findings')
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'vendor_assessment_finding_id'
            );

        $this->table('vendor_assessment_findings')
            ->dropForeignKey(
                'vendor_assessment_id'
            );

        $this->dropTable('users_vendor_assessment_findings');

        $this->dropTable('vendor_assessment_findings');
    }
}

