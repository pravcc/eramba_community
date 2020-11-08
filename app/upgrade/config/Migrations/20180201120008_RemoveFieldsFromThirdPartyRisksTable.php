<?php
use Phinx\Migration\AbstractMigration;

class RemoveFieldsFromThirdPartyRisksTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Owner' => 'user_id',
                'Stakeholder' => 'guardian_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'ThirdPartyRisk', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('third_party_risks')
            ->dropForeignKey('guardian_id')
            ->dropForeignKey('user_id')
            ->removeIndexByName('guardian_id')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('third_party_risks')
            ->removeColumn('user_id')
            ->removeColumn('guardian_id')
            ->update();
    }

    public function down()
    {
        $this->table('third_party_risks')
            ->addColumn('user_id', 'integer', [
                'after' => 'residual_risk_formula',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addColumn('guardian_id', 'integer', [
                'after' => 'user_id',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'guardian_id',
                ],
                [
                    'name' => 'guardian_id',
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

        $this->table('third_party_risks')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'guardian_id',
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

