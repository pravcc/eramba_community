<?php
use Phinx\Migration\AbstractMigration;

class RemoveFieldsFromBusinessContinuityPlansTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'LaunchInitiator' => 'launch_responsible_id',
                'Sponsor' => 'sponsor_id',
                'Owner' => 'owner_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'BusinessContinuityPlan', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('business_continuity_plans')
            ->dropForeignKey('launch_responsible_id')
            ->dropForeignKey('owner_id')
            ->dropForeignKey('sponsor_id')
            ->removeIndexByName('launch_responsible_id')
            ->removeIndexByName('owner_id')
            ->removeIndexByName('sponsor_id')
            ->update();

        $this->table('business_continuity_plans')
            ->removeColumn('launch_responsible_id')
            ->removeColumn('sponsor_id')
            ->removeColumn('owner_id')
            ->update();
    }

    public function down()
    {
        $this->table('business_continuity_plans')
            ->addColumn('launch_responsible_id', 'integer', [
                'after' => 'ongoing_corrective_actions',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('sponsor_id', 'integer', [
                'after' => 'launch_responsible_id',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'sponsor_id',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'launch_responsible_id',
                ],
                [
                    'name' => 'launch_responsible_id',
                ]
            )
            ->addIndex(
                [
                    'owner_id',
                ],
                [
                    'name' => 'owner_id',
                ]
            )
            ->addIndex(
                [
                    'sponsor_id',
                ],
                [
                    'name' => 'sponsor_id',
                ]
            )
            ->update();

        $this->table('business_continuity_plans')
            ->addForeignKey(
                'launch_responsible_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'sponsor_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

