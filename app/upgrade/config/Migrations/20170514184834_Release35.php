<?php
use Phinx\Migration\AbstractMigration;

class Release35 extends AbstractMigration
{
    protected function bumpVersion($value) {
        $this->query("UPDATE `settings` SET `value`='" . $value . "' WHERE `settings`.`variable`='DB_SCHEMA_VERSION'");

        if (class_exists('App')) {
            App::uses('Configure', 'Core');

            if (class_exists('Configure')) {
                Configure::write('Eramba.Settings.DB_SCHEMA_VERSION', $value);
            }
        }
    }

    public function up()
    {
        $this->bumpVersion('e1.0.1.018');
    }

    public function down()
    {
        $this->bumpVersion('e1.0.1.017');
    }
}
