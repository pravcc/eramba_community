<?php
use Phinx\Migration\AbstractMigration;

class AddSecuritySaltSettings extends AbstractMigration
{
    protected $group = 'SEC';
    protected $variable = 'SECURITY_SALT';
    protected $slug = 'SECSALT';

    public function up()
    {
        // Insert new setting groups
        $settingGroupRows = [
            [
                'slug' => $this->slug,
                'parent_slug' => $this->group,
                'name' => 'Security Salt',
                'icon_code' => null,
                'notes' => null,
                'url' => null,
                'hidden' => 1,
                'order' => 0
            ]
        ];

        $table = $this->table('setting_groups');
        $table->insert($settingGroupRows);
        $table->saveData();

        App::uses('CakeText', 'Utility');

        // Insert new settings
        $settingRows = [
            'active' => 1,
            'name' => 'Security Salt',
            'variable' => $this->variable,
            'value' => CakeText::uuid(),
            'default_value' => null,
            'values' => null,
            'type' => 'text',
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
