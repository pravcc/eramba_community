<?php
use Phinx\Migration\AbstractMigration;

class Release36 extends AbstractMigration
{

    protected function bumpVersion($value) {
        $this->query("UPDATE `settings` SET `value`='" . $value . "' WHERE `settings`.`variable`='DB_SCHEMA_VERSION'");

        if (class_exists('App')) {
            App::uses('Configure', 'Core');

            if (class_exists('Configure')) {
                Configure::write('Eramba.Settings.DB_SCHEMA_VERSION', $value);
            }

            $cacheDisable = Configure::read('Cache.disable');
            Configure::write('Cache.disable', true);

            App::uses('ConnectionManager', 'Model');
            App::uses('VisualisationShell', 'Visualisation.Console/Command');
            App::uses('ObjectVersionShell', 'ObjectVersion.Console/Command');
            App::uses('ClassRegistry', 'Utility');

            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;

            $Setting = ClassRegistry::init('Setting');
            $Setting->deleteCache(null);

            App::uses('AppModule', 'Lib');
            AppModule::loadAll();

            if (Configure::read('Eramba.version') === 'e1.0.6.035') {
                $VisualisationShell = new VisualisationShell();
                $VisualisationShell->startup();

                $VisualisationShell->compliance_sync();
                $VisualisationShell->acl_sync();

                $ObjectVersionShell = new ObjectVersionShell();
                $ObjectVersionShell->startup();
                $ObjectVersionShell->add_versioning();

                Configure::write('Cache.disable', $cacheDisable);
            }

            // App::uses('ComplianceManagement', 'Model');
            // App::uses('Visualisation', 'Visualisation.Lib');
            // App::uses('CustomRoles', 'CustomRoles.Lib');

            // $Compliance = ClassRegistry::init('ComplianceManagement');
            // $Compliance->syncObjects();

            // $Visualisation = new Visualisation();
            // $CustomRoles = new CustomRoles();
            
        }
    }

    public function up()
    {
        $this->bumpVersion('e1.0.1.019');
    }

    public function down()
    {
        $this->bumpVersion('e1.0.1.018');
    }
}

