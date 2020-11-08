<?php
use Phinx\Migration\AbstractMigration;

class AddClientKeyToSettings extends AbstractMigration
{
    protected $group = 'SEC';
    protected $variable = 'CLIENT_KEY';
    protected $slug = 'ENTERPRISE_USERS';

    public function up()
    {
        // Insert new setting groups
        $settingGroupRows = [
            [
                'slug' => $this->slug,
                'parent_slug' => $this->group,
                'name' => 'Enterprise Users',
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
            'name' => 'Enterprise Activation Key',
            'variable' => $this->variable,
            'value' => $this->getClientKeyFromFile(),
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

    protected function getClientKeyFromFile()
    {
        if (class_exists('App')) {
            $keyFile = new File(APP . DS . 'Vendor' . DS . 'other' . DS . 'CLIENT_KEY');

            if ($keyFile->exists()) {
                return trim($keyFile->read());
            }
        }

        return null;
    }

    public function down()
    {
        $this->query("DELETE FROM `setting_groups` WHERE (`slug` = '{$this->slug}')");

        $this->query("DELETE FROM `settings` WHERE (`variable` = '{$this->variable}')");
    }
}
