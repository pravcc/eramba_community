<?php
use Phinx\Migration\AbstractMigration;

class ImportToolSectionFulltextIndexes2 extends AbstractMigration
{
    public function up()
    {
        $this->table('risks')
            ->addIndex('title', ['type' => 'fulltext'])
            ->update();

        $this->table('third_party_risks')
            ->addIndex('title', ['type' => 'fulltext'])
            ->update();

        $this->table('business_continuities')
            ->addIndex('title', ['type' => 'fulltext'])
            ->update();

        $this->table('security_services')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('assets')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('threats')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('vulnerabilities')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('risk_exceptions')
            ->addIndex('title', ['type' => 'fulltext'])
            ->update();

        $this->table('processes')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('business_continuity_plans')
            ->addIndex('title', ['type' => 'fulltext'])
            ->update();
    }
}
