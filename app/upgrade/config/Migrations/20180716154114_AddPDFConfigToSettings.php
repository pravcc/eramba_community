<?php
use Phinx\Migration\AbstractMigration;

class AddPDFConfigToSettings extends AbstractMigration
{
	protected $group = 'GENERAL';
	protected $variable = 'PDF_PATH_TO_BIN';
    protected $slug = 'PDFCONFIG';

    public function up()
    {
        // Insert new setting groups
        $settingGroupRows = [
            [
            	'slug' => $this->slug,
                'parent_slug' => $this->group,
                'name' => 'PDF Configuration',
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
            'name' => 'WKHTMLTOPDF path to bin file',
            'variable' => $this->variable,
            'value' => '/usr/local/bin/wkhtmltopdf',
            'default_value' => '/usr/local/bin/wkhtmltopdf',
            'values' => null,
            'type' => 'text',
            'options' => null,
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
        $this->query("DELETE FROM `setting_groups` WHERE (`slug` = '{$this->slug}')");

        $this->query("DELETE FROM `settings` WHERE (`variable` = '{$this->variable}')");
    }
}

