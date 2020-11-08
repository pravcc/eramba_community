<?php
use Phinx\Migration\AbstractMigration;

class CustomLabelsDescriptionMigration extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('custom_labels');
        $column = $table->hasColumn('description');

        if (!$column) {
            $this->table('custom_labels')
                ->addColumn('description', 'text', [
                    'after' => 'label',
                    'default' => null,
                    'length' => null,
                    'null' => true,
                ])
                ->update();
        }
    }
    public function down()
    {
        $table = $this->table('custom_labels');
        $column = $table->hasColumn('description');

        if ($column) {
            $this->table('custom_labels')
                ->removeColumn('description')
                ->update();
        }
    }
}
