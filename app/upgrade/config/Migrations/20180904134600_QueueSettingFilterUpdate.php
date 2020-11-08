<?php
use Phinx\Migration\AbstractMigration;

class QueueSettingFilterUpdate extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        // Update queue index link
        $this->query("UPDATE `setting_groups` SET `url`='{\"controller\":\"queue\", \"action\":\"index\"}' WHERE `slug`='QUEUE'");
    }

    public function down()
    {
        $this->query("UPDATE `setting_groups` SET `url`='{\"controller\":\"queue\", \"action\":\"index\", \"?\":\"advanced_filter=1\"}' WHERE `slug`='QUEUE'");
    }
}
