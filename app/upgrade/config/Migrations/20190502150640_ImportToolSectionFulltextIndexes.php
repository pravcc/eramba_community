<?php
use Phinx\Migration\AbstractMigration;

class ImportToolSectionFulltextIndexes extends AbstractMigration
{
    public function up()
    {
        $this->table('business_units')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('users')
            ->addIndex('login', ['type' => 'fulltext'])
            ->update();

        $this->table('groups')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('legals')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('asset_labels')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('asset_media_types')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('compliance_package_regulators')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('security_policies')
            ->addIndex('index', ['type' => 'fulltext'])
            ->update();

        $this->table('third_parties')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();

        $this->table('projects')
            ->addIndex('title', ['type' => 'fulltext'])
            ->update();

        $this->table('security_policy_document_types')
            ->addIndex('name', ['type' => 'fulltext'])
            ->update();
    }
}
