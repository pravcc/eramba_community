<?php
use Phinx\Migration\AbstractMigration;

class RemoveFieldsFromSecurityServiceMaintenancesTable extends AbstractMigration
{
    protected function migrateData($type)
    {
        if (class_exists('App')) {
            App::uses('UserFields', 'UserFields.Lib');
            $UserFields = new UserFields();

            $fields = [
                'MaintenanceOwner' => 'user_id'
            ];
            $UserFields->moveExistingFieldsToUserFieldsTable($type, 'SecurityServiceMaintenance', $fields);
        }
    }

    public function up()
    {
        $this->migrateData('up');

        $this->table('security_service_maintenances')
            ->dropForeignKey([], 'security_service_maintenances_ibfk_4')
            ->removeIndexByName('user_id')
            ->update();

        $this->table('security_service_maintenances')
            ->removeColumn('user_id')
            ->update();
    }

    public function down()
    {
        $this->table('security_service_maintenances')
            ->addColumn('user_id', 'integer', [
                'after' => 'task_conclusion',
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

        $this->table('security_service_maintenances')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'security_service_maintenances_ibfk_4'
                ]
            )
            ->update();

        $this->migrateData('down');
    }
}

