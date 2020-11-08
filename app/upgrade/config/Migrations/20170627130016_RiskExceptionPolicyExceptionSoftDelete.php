<?php
use Phinx\Migration\AbstractMigration;

class RiskExceptionPolicyExceptionSoftDelete extends AbstractMigration
{

    public function up()
    {

        $this->table('policy_exceptions')
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

        $this->table('risk_exceptions')
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

        $this->table('policy_exceptions')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('risk_exceptions')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

