<?php
use Phinx\Migration\AbstractMigration;

class Release37 extends AbstractMigration
{

    protected function bumpVersion($value) {
    	$ret = true;

        $this->query("UPDATE `settings` SET `value`='" . $value . "' WHERE `settings`.`variable`='DB_SCHEMA_VERSION'");

        if (class_exists('App')) {
        	$status = [];
            App::uses('Configure', 'Core');

            if (class_exists('Configure')) {
                Configure::write('Eramba.Settings.DB_SCHEMA_VERSION', $value);
            }

            // testing handler for exception
            if (Configure::read('Eramba.TRIGGER_UPDATE_FAIL') === true) {
            	$status['ConfiguredFailTriggered'] = true;
            	throw new Exception("This is a test exception for failed update.", 1);
            	return false;
            }

            $cacheDisable = Configure::read('Cache.disable');
            Configure::write('Cache.disable', true);

            App::uses('ConnectionManager', 'Model');
            App::uses('VisualisationShell', 'Visualisation.Console/Command');
            App::uses('ClassRegistry', 'Utility');

            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;

            $Setting = ClassRegistry::init('Setting');
            $Setting->deleteCache(null);

            $purifierCache = APP . 'Plugin/HtmlPurifier/Vendor/HtmlPurifier/library/HTMLPurifier/DefinitionCache/Serializer/';
			$purifierFiles = glob($purifierCache . '{' . implode(',', array('.', '*')) . '}' . DS . '*', GLOB_BRACE);
			if(!empty($files)){
				foreach ($files as $file) {
					if (is_file($file) && basename($file) !== 'empty') {
						@unlink($file);
					}
				}
			}

            App::uses('AppModule', 'Lib');
            AppModule::loadAll();

            // $NotificationSystem = ClassRegistry::init('NotificationSystem');
            // $NotificationCustomRoles = $NotificationSystem->NotificationCustom;

            // $ret &= $NotificationCustomRoles->deleteAll([
            // 	$NotificationCustomRoles->escapeField('migration_updated') => '0'
            // ]);

            // remove not needed Aro rows for previous slow version of custom roles
            $Aro = ClassRegistry::init('Aro');
            $ret &= $Aro->deleteAll([
            	$Aro->escapeField('model') => 'CustomRolesRole'
            ]);
            $status['AroDelete'] = $ret;

            $VisualisationShell = new VisualisationShell();
            $VisualisationShell->startup();

            if (Configure::read('Eramba.version') === 'e1.0.6.036') {
                // lets synchronize new upgraded custom roles
                $ret &= $VisualisationShell->CustomRoles->sync();
                $status['CustomRoles'] = $ret;
            }

            // recover aro tree if corrupted
            // $ret &= $VisualisationShell->CustomRoles->Acl->Aro->recover();
            // $status['RecoverAro'] = $ret;
            
            // $ret &= $VisualisationShell->CustomRoles->Acl->Aco->recover();
            // $status['RecoverAco'] = $ret;

            // make sure group for auditees exists temporarily until new acl is in place
            $Group = ClassRegistry::init('Group');
            $auditeeGroup = $Group->find('count', [
            	'conditions' => [
            		$Group->escapeField('id') => 11
            	],
            	'recursive' => -1
            ]);

            if (!$auditeeGroup) {
            	$Group->create();
            	$Group->set([
            		'id' => 11,
            		'name' => 'Third Party Feedback',
            		'description' => '',
            		'status' => 1
            	]);

            	$ret &= $Group->save();
            	$status['Group'] = $ret;
            }

            $ret &= $this->deleteFiles();
            $status['DeleteFiles'] = $ret;

            Configure::write('Cache.disable', $cacheDisable);

            $Setting->deleteCache(null);
        }

        if (!$ret) {
        	App::uses('CakeLog', 'Log');
        	$log = "Error occured when processing database synchronization for release 1.0.6.037.";
        	CakeLog::write('error', "{$log} \n" . print_r($status, true));

        	throw new Exception($log, 1);
        	return false;
        }
    }

    public function up()
    {
        $this->bumpVersion('e1.0.1.020');
    }

    public function down()
    {
        $this->bumpVersion('e1.0.1.019');
    }

    private function deleteFiles() {
    	App::uses('File', 'Utility');
    	App::uses('Folder', 'Utility');

		$filesList = $this->getFilesToDelete();

		$ret = true;
		foreach ($filesList as $line) {
			$line = trim($line);

			if ($line == '' || strlen($line) < 2) {
				continue;
			}

			$path = ROOT . DS . $line;

			// directory check and file check before trying to delete it, otherwise it would fail.
			if (is_dir($path)) {
				$folder = new Folder($path);

				// pwd() cannot be null
				if ($folder->pwd() !== null) {
					$ret &= $folder->delete();
				}
			}
			elseif (file_exists($path)) {
				$file = new File($path);

				// additional check via cakephp
				if ($file->exists()) {
					$ret &= $file->delete();
				}
			}
		}

		return $ret;
	}

	public function getFilesToDelete() {
		return [
			'app/Controller/Component/CustomFieldsMgtComponent.php',
			'app/Controller/Component/EmailDebugComponent.php',
			'app/Controller/Component/LdapComponent.php',
			'app/Controller/Component/MappingComponent.php',
			'app/Controller/Component/SettingsComponent.php',
			'app/Controller/BackupRestoreController.php',
			'app/Controller/CustomFieldSettingsController.php',
			'app/Controller/CustomFieldsController.php',
			'app/Controller/CustomFormsController.php',
			'app/Lib/FieldData/FieldGroupEntity.php',
			'app/Lib/FieldData/',
			'app/Model/Audit.php',
			'app/Model/Behavior/CustomFieldsBehavior.php',
			'app/Model/Behavior/MappingBehavior.php',
			'app/Model/Behavior/StatusAssessmentBehavior.php',
			'app/Model/Behavior/WorkflowActiveModelBehavior.php',
			'app/Model/Behavior/WorkflowInactiveModelBehavior.php',
			'app/Model/CustomField.php',
			'app/Model/CustomFieldOption.php',
			'app/Model/CustomFieldSetting.php',
			'app/Model/CustomFieldValue.php',
			'app/Model/CustomForm.php',
			'app/View/CustomFieldSettings/add.ctp',
			'app/View/CustomFieldSettings/',
			'app/View/CustomFields/add.ctp',
			'app/View/CustomFields/delete.ctp',
			'app/View/CustomFields/warning.ctp',
			'app/View/CustomFields/',
			'app/View/CustomForms/add.ctp',
			'app/View/CustomForms/delete.ctp',
			'app/View/CustomForms/index.ctp',
			'app/View/CustomForms/',
			'app/View/Elements/customFields/display/accordion.ctp',
			'app/View/Elements/customFields/fieldTypes/date.ctp',
			'app/View/Elements/customFields/fieldTypes/dropdown.ctp',
			'app/View/Elements/customFields/fieldTypes/paragraph.ctp',
			'app/View/Elements/customFields/fieldTypes/short_text.ctp',
			'app/View/Elements/customFields/options.ctp',
			'app/View/Elements/customFields/tabs.ctp',
			'app/View/Elements/customFields/tabs_content.ctp',
			'app/View/Elements/customFields/',
			'app/View/Elements/messages/ajax-flash-error.ctp',
			'app/View/Elements/messages/ajax-flash-info.ctp',
			'app/View/Elements/messages/ajax-flash-ok.ctp',
			'app/View/Elements/messages/ajax-flash-warning.ctp',
			'app/View/Elements/messages/flash-error.ctp',
			'app/View/Elements/messages/flash-info.ctp',
			'app/View/Elements/messages/flash-ok.ctp',
			'app/View/Elements/messages/flash-warning.ctp',
			'app/View/Elements/messages/',
			'app/View/Helper/CustomFieldsHelper.php'
		];
	}
}

