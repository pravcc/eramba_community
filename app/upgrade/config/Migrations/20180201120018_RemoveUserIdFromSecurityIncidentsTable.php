<?php
use Phinx\Migration\AbstractMigration;

class RemoveUserIdFromSecurityIncidentsTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'Owner' => 'user_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'SecurityIncident', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('security_incidents')
            ->dropForeignKey([], 'security_incidents_ibfk_5')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('security_incidents')
            ->removeColumn('user_id')
            ->update();
    }

    public function down()
    {

        $this->table('security_incidents')
            ->addColumn('user_id', 'integer', [
                'after' => 'description',
                'default' => 1,
                'length' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'user_id',
                ]
            )
            ->update();

        $this->table('security_incidents')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT',
                    'constraint' => 'security_incidents_ibfk_5'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

