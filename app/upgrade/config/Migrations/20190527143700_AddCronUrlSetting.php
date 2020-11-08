<?php
use Phinx\Migration\AbstractMigration;

class AddCronUrlSetting extends AbstractMigration
{
    protected $variable = 'CRON_URL';
    protected $slug = 'SECKEY';

    public function up()
    {
        // Insert new settings
        $settingRows = [
            'active' => 1,
            'name' => 'Cron URL',
            'variable' => $this->variable,
            'value' => '',
            'default_value' => null,
            'values' => null,
            'type' => 'text',
            // 'options' => '{"web":"Web","cli":"CLI"}',
            'hidden' => 0,
            'required' => 1,
            'setting_group_slug' => $this->slug,
            'setting_type' => 'constant',
            'order' => 1,
            'modified' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s')
        ];

        $table = $this->table('settings');
        $table->insert($settingRows);
        $table->saveData();


        $this->query("UPDATE `settings` SET `order` = '2' WHERE (`variable` = 'CRON_SECURITY_KEY')");
    }

    public function down()
    {
        $this->query("DELETE FROM `settings` WHERE (`variable` = '{$this->variable}')");
    }
}