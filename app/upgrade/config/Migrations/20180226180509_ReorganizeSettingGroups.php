<?php
use Phinx\Migration\AbstractMigration;

class ReorganizeSettingGroups extends AbstractMigration
{
    public function up()
    {
        // Insert new setting group
        $rows = [
            [
                'slug' => 'CRONJOBS',
                'parent_slug' => null,
                'name' => 'Cron Jobs',
                'icon_code' => 'icon-cog',
                'notes' => null,
                'url' => null,
                'hidden' => 0,
                'order' => 0
            ]
        ];

        $table = $this->table('setting_groups');
        $table->insert($rows);
        $table->saveData();

        // Change parent of existing records
        $this->execute("UPDATE `setting_groups` SET `parent_slug`='CRONJOBS', `name`='Crontab History' WHERE `slug`='CRON'");
        $this->execute("UPDATE `setting_groups` SET `parent_slug`='CRONJOBS', `name`='Crontab Security Key' WHERE `slug`='SECKEY'");
    }

    public function down()
    {
        $this->execute("UPDATE `setting_groups` SET `parent_slug`='ACCESSMGT', `name`='Cron Jobs' WHERE `slug`='CRON'");
        $this->execute("UPDATE `setting_groups` SET `parent_slug`='SEC', `name`='Security Key' WHERE `slug`='SECKEY'");
        $this->execute("DELETE FROM `setting_groups` WHERE `slug` = 'CRONJOBS'");
    }
}
