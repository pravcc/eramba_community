<?php
use Phinx\Migration\AbstractMigration;

class AddSSLOffloadToSettings extends AbstractMigration
{
    protected $group = 'SEC';
    protected $variable = 'SSL_OFFLOAD_ENABLED';
    protected $slug = 'SSLOFFLOAD';

    public function up()
    {
        // Insert new setting groups
        $settingGroupRows = [
            [
                'slug' => $this->slug,
                'parent_slug' => $this->group,
                'name' => 'SSL/TLS Offload',
                'icon_code' => null,
                'notes' => null,
                'url' => null,
                'hidden' => 0,
                'order' => 0
            ]
        ];

        $table = $this->table('setting_groups');
        $table->insert($settingGroupRows);
        $table->saveData();

        // Insert new settings
        $settingRows = [
            'active' => 1,
            'name' => 'Enable SSL/TLS Offload Requests',
            'variable' => $this->variable,
            'value' => '0',
            'default_value' => '0',
            'values' => null,
            'type' => 'checkbox',
            'options' => null,
            'hidden' => 0,
            'required' => 0,
            'setting_group_slug' => $this->slug,
            'setting_type' => 'config',
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
        $this->query("DELETE FROM `setting_groups` WHERE (`slug` = '{$this->slug}')");

        $this->query("DELETE FROM `settings` WHERE (`variable` = '{$this->variable}')");
    }
}
