<?php
use Phinx\Migration\AbstractMigration;

class SettingDefaultCronUrlMigration extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
            App::uses('Configure', 'Core');

            $Settings = ClassRegistry::init('Setting');

            $setting = $Settings->find('first', [
                'conditions' => [
                    'Setting.variable' => 'CRON_URL'
                ],
                'fields' => [
                    'Setting.id', 'Setting.variable', 'Setting.value'
                ],
                'recursive' => -1
            ]);

            if (!empty($setting) && empty($setting['Setting']['value'])) {
                $this->query("UPDATE `settings` SET `value`='" . Configure::read('App.fullBaseUrl') . "' WHERE `settings`.`variable`='CRON_URL'");
            }
        }
    }

    public function down()
    {
    }
}
