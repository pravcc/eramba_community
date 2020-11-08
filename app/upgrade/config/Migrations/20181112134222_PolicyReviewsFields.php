<?php
use Phinx\Migration\AbstractMigration;

class PolicyReviewsFields extends AbstractMigration
{

    public function up()
    {

        $this->table('reviews')
            ->addColumn('use_attachments', 'integer', [
                'after' => 'completed',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('policy_description', 'text', [
                'after' => 'use_attachments',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('url', 'text', [
                'after' => 'policy_description',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('reviews')
            ->removeColumn('use_attachments')
            ->removeColumn('url')
            ->removeColumn('policy_description')
            ->update();
    }
}

