<?php
use Phinx\Migration\AbstractMigration;

class ProgramSectionObjectVersion extends AbstractMigration
{

    public function up()
    {

        $this->table('goals')
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

        $this->table('program_issues')
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

        $this->table('program_scopes')
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

        $this->table('team_roles')
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

        $this->table('goals')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('program_issues')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('program_scopes')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('team_roles')
            ->removeColumn('deleted')
            ->removeColumn('deleted_date')
            ->update();
    }
}

