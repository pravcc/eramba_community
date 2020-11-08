<?php
use Phinx\Migration\AbstractMigration;

class RemoveAuthorIdFromRiskExceptionsTable extends AbstractMigration
{  
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Requester' => 'author_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'RiskException', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('risk_exceptions')
            ->dropForeignKey([], 'FK_risk_exceptions_users')
            ->removeIndexByName('FK_risk_exceptions_users')
            ->update();

        $this->table('risk_exceptions')
            ->removeColumn('author_id')
            ->update();
    }

    public function down()
    {
        $this->table('risk_exceptions')
            ->addColumn('author_id', 'integer', [
                'after' => 'description',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'author_id',
                ],
                [
                    'name' => 'FK_risk_exceptions_users',
                ]
            )
            ->update();

        $this->table('risk_exceptions')
            ->addForeignKey(
                'author_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'FK_risk_exceptions_users'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

