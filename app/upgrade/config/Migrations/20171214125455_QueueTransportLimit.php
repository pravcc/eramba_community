<?php
use Phinx\Migration\AbstractMigration;

class QueueTransportLimit extends AbstractMigration
{
    // rowID crashed because of custom logo
    // public $rowID = 31;
    public $variable = 'QUEUE_TRANSPORT_LIMIT';

    public function up()
    {
        $this->insertData();
    }

    protected function insertData() {
        // insert custom field settings
        $rows = [
            'active' => 1,
            'name' => 'Email Queue Throughput',
            'variable' => $this->variable,
            'value' => '15',
            'default_value' => NULL,
            'values' => NULL,
            'type' => 'number',
            'options' => NULL,
            'hidden' => 0,
            'required' => 0,
            'setting_group_slug' => 'MAILCNF',
            'setting_type' => 'constant',
            'order' => 8,
            'modified' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s')
        ];

        $table = $this->table('settings');
        $table->insert($rows);
        $table->saveData();
    }

    public function down() {
        $this->query("DELETE FROM `settings` WHERE (`variable` = '{$this->variable}')");
    }
}
