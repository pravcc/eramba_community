<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentParentMigration extends AbstractMigration
{

    public function up()
    {

        $this->table('vendor_assessments')
            ->addColumn('parent_id', 'integer', [
                'after' => 'id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'parent_id',
                ],
                [
                    'name' => 'parent_id',
                ]
            )
            ->update();

        $this->table('vendor_assessments')
            ->addForeignKey(
                'parent_id',
                'vendor_assessments',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET_NULL'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('vendor_assessments')
            ->dropForeignKey(
                'parent_id'
            );

        $this->table('vendor_assessments')
            ->removeIndexByName('parent_id')
            ->update();

        $this->table('vendor_assessments')
            ->removeColumn('parent_id')
            ->update();
    }
}

