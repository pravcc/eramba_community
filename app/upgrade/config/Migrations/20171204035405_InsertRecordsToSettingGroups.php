<?php
use Phinx\Migration\AbstractMigration;

class InsertRecordsToSettingGroups extends AbstractMigration
{
    public function up()
    {
        // Insert new setting group
        $rows = [
            [
                'slug' => 'OAUTH',
                'parent_slug' => 'ACCESSMGT',
                'name' => 'OAuth Connectors',
                'icon_code' => null,
                'notes' => null,
                'url' => '{"controller":"oauthConnectors","action":"index"}',
                'hidden' => 0,
                'order' => 0
            ]
        ];

        $table = $this->table('setting_groups');
        $table->insert($rows);
        $table->saveData();
    }

    public function down()
    {
        $this->query("DELETE FROM `setting_groups` WHERE (`slug` = 'OAUTH')");
    }
}
