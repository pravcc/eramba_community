<?php
use Phinx\Migration\AbstractMigration;

class NewTemplateSettings extends AbstractMigration
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
        $this->table('setting_groups')
            ->addColumn('modal', 'integer', [
                'after' => 'url',
                'default' => '0',
                'length' => 1,
                'null' => false
            ])
            ->update();

        // Add data to new modal column of setting_groups table
        $this->query("UPDATE `setting_groups` SET `modal`=1 WHERE `slug`='AUTH' OR `slug`='BAR' OR `slug`='DBRESET' OR `slug`='ERRORLOG' OR `slug`='MAILLOG' OR `slug`='LOGO' OR `slug`='HEALTH'");

        // Update column hidden in setting_groups table
        $this->query("UPDATE `setting_groups` SET `hidden`=1 WHERE `slug`='NOTIFICATION' OR `slug`='ROLES'");
    }

    public function down()
    {
        $this->table('setting_groups')
            ->removeColumn('modal')
            ->update();

        // Update column hidden in setting_groups table
        $this->query("UPDATE `setting_groups` SET `hidden`=0 WHERE `slug`='NOTIFICATION' OR `slug`='ROLES'");
    }
}
