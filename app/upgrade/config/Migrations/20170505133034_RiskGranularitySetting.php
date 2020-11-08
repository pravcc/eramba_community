<?php
use Phinx\Migration\AbstractMigration;

class RiskGranularitySetting extends AbstractMigration
{
    // rowID crashed because of custom logo
    // public $rowID = 31;
    public $variable = 'RISK_GRANULARITY';

    public function up()
    {
        $this->insertData();

    }

    protected function insertData() {
        // insert custom field settings
        $rows = [
            // 'id' => $this->rowID,
            'active' => 1,
            'name' => 'Risk Granularity',
            'variable' => $this->variable,
            'value' => '10',
            'default_value' => NULL,
            'values' => NULL,
            'type' => 'number',
            'options' => NULL,
            'hidden' => 0,
            'required' => 0,
            'setting_group_slug' => NULL,
            'setting_type' => 'constant',
            'order' => 0,
            'modified' => '2017-04-19 00:00:00',
            'created' => '2017-04-19 00:00:00'
        ];

        $table = $this->table('settings');
        $table->insert($rows);
        $table->saveData();
    }

    public function down() {
        $this->query("DELETE FROM `settings` WHERE (`variable` = '{$this->variable}')");
    }
}
