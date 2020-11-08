<?php
use Phinx\Migration\AbstractMigration;

class HideDashboardSetting extends AbstractMigration
{
    public function up()
    {
        $this->hideDashboard(0);
    }

    public function down()
    {
        $this->hideDashboard(1);
    }

    protected function hideDashboard($hide = 1) {
        $this->query("UPDATE `setting_groups` SET `hidden`='{$hide}' WHERE `setting_groups`.`slug`='DASH'");
    }
}
