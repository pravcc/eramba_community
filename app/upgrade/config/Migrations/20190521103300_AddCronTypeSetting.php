<?php
use Phinx\Migration\AbstractMigration;

class AddCronTypeSetting extends AbstractMigration
{
    protected $variable = 'CRON_TYPE';
    protected $slug = 'SECKEY';

    public function up()
    {
        // Insert new settings
        $settingRows = [
            'active' => 1,
            'name' => 'Cron Type',
            'variable' => $this->variable,
            'value' => 'web',
            'default_value' => null,
            'values' => null,
            'type' => 'select',
            'options' => '{"web":"Web","cli":"CLI"}',
            'hidden' => 0,
            'required' => 0,
            'setting_group_slug' => $this->slug,
            'setting_type' => 'constant',
            'order' => 0,
            'modified' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s')
        ];

        $table = $this->table('settings');
        $table->insert($settingRows);
        $table->saveData();
    }

    public function down()
    {
        $this->query("DELETE FROM `settings` WHERE (`variable` = '{$this->variable}')");
    }
}