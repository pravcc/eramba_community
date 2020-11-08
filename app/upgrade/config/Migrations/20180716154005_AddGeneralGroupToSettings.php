<?php
use Phinx\Migration\AbstractMigration;

class AddGeneralGroupToSettings extends AbstractMigration
{
    public function up()
    {
        // Insert new setting groups
        $settingGroupRows = [
            [
                'slug' => 'GENERAL',
                'parent_slug' => null,
                'name' => 'General Settings',
                'icon_code' => 'icon-cog',
                'notes' => null,
                'url' => null,
                'hidden' => 0,
                'order' => 0
            ]
        ];

        $table = $this->table('setting_groups');
        $table->insert($settingGroupRows);
        $table->saveData();
    }

    public function down()
    {
        $this->query("DELETE FROM `setting_groups` WHERE (`slug` = 'GENERAL')");
    }
}
