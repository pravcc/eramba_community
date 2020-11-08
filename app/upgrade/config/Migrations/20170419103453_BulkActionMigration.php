<?php
use Phinx\Migration\AbstractMigration;

class BulkActionMigration extends AbstractMigration
{

    public function up()
    {
        $this->table('security_service_maintenances')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('assets')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('business_continuities')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('reviews')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('risks')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('security_policies')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('security_service_audits')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('security_services')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('third_party_risks')
            ->addColumn('deleted', 'integer', [
                'after' => 'modified',
                'default' => '0',
                'length' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'deleted',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('security_service_maintenances')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('assets')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('business_continuities')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('reviews')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('risks')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('security_policies')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('security_service_audits')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('security_services')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('third_party_risks')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

