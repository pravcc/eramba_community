<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('Router', 'Routing');
App::uses('CakeLog', 'Log');
App::uses('SystemHealthLib', 'Lib');
App::uses('AutoUpdateLib', 'Lib');
App::uses('Setting', 'Model');

class SettingsController extends AppController {
	public $helpers = array( 'Html', 'Form' );
	public $components = array('Paginator', 'Ajax' => array(
		// 'actions' => array('residualRisk')
	),
	'Crud.Crud' => [
			'actions' => [
				'residualRisk' => [
					'className' => 'AppEdit',
					'enabled' => true
				]
			]
		]
	);
	public $name = 'Settings';

	public function beforeFilter()
	{
		$this->Auth->allow('getLogo', 'testPdf');

		if ($this->request->params['action'] == 'deleteCache') {
			Configure::write('Cache.disable', true);
		}

		parent::beforeFilter();

		// if ($this->request->params['action'] == 'customLogo') {
		// 	$this->Security->validatePost = true;
		// }
		
		// We don't need to reload any content after form is submitted
		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-success-reload', '');

		$this->Security->unlockedActions = ['testMailConnection', 'getTimeByTimezone'];
	}

	public function index()
	{
		$this->set('title_for_layout', __('Settings' ));
		$this->set('subtitle_for_layout', __('System Settings'));

		$this->set('settings', $this->_getAvailableSettingsList());
	}

	/**
	 * Get list of settings available for logged user.
	 * 
	 * @return array List of available settings.
	 */
	protected function _getAvailableSettingsList()
	{
		$settings = [];

		foreach (Setting::groupsConfig(true) as $settingsGroupKey => $settingsGroup) {
			$availableItems = [];

			foreach ($settingsGroup['children'] as $settingItemKey => $settingItem) {
				if (!empty($settingItem['url'])) {
					$url = $settingItem['url'];
				}
				else {
					$url = ['controller' => 'settings', 'action' => 'edit', $settingItemKey];
				}

				$hasRights = $this->AppAcl->check($url, $this->logged['Groups']);

				if ($hasRights) {
					$availableItems[$settingItemKey] = $settingItem;
				}
			}

			if (!empty($availableItems)) {
				$settings[$settingsGroupKey] = $settingsGroup;
				$settings[$settingsGroupKey]['children'] = $availableItems;
			}
		}

		return $settings;
	}

	public function edit($groupSlug = null)
	{
		$settingGroup = Setting::getGroupWithSettings($groupSlug);

		if (empty($settingGroup)) {
			throw new NotFoundException();
		}

		$this->Ajax->initModal('normal', __('Edit Settings (%s)', $settingGroup['name']));
		$this->Modals->changeConfig('footer.buttons.saveBtn.visible', true);

		$this->set('formName', 'Setting');
		$this->set('formUrl', Router::url());

		if ($this->request->is(array('post', 'put'))) {
			$db = $this->Setting->getDataSource();
			$db->begin();

			$ret = true;
			$state = 'success';
			$allowed = array_keys($settingGroup['settings']);
			
			foreach ($this->request->data['Setting'] as $key => $value) {
				if(in_array($key, $allowed)){
					$ret &= $this->manageBeforeSaveCallbacks($key, $value);

					$ret &= $this->Setting->updateVariable($key, $value);

					$ret &= $this->manageCallbacks($key, $value);
				}
			}

			if ($ret) {
				$db->commit();
				Cache::delete('settings', 'long');

				$state = 'success';
			}
			else {
				$db->rollback();
				$this->Session->setFlash( __( 'Error while saving the data. Please try it again.' ), FLASH_ERROR );

				$state = 'error';
			}

			$this->YoonityJSConnector->setState($state);
		}
		else {
			$this->_setSettingsRequestData($settingGroup);
		}

		$this->set('settingGroup', $settingGroup);
		$this->set('slug', $groupSlug);

		$this->render('edit');
	}

	public function backup()
	{
		$this->edit('BACKUP');
	}

	public function debug()
	{
		$this->edit('DEBUGCFG');
	}

	public function currency()
	{
		$this->edit('CUE');
	}

	public function timezone()
	{
		$this->edit('TZONE');
	}

	public function csv()
	{
		$this->edit('CSV');
	}

	public function email()
	{
		$this->edit('MAILCNF');
	}

	public function bruteForceProtection()
	{
		$this->edit('BFP');
	}

	public function sslOffload()
	{
		$this->edit('SSLOFFLOAD');
	}

	public function enterpriseUser()
	{
		$this->edit('ENTERPRISE_USERS');
	}

	public function crontab()
	{
		$this->edit('SECKEY');
	}

	public function pdf()
	{
		$this->edit('PDFCONFIG');
	}

	protected function _setSettingsRequestData($settingGroup)
	{
		$settings = $this->Setting->find('list', [
			'fields' => ['Setting.variable', 'Setting.value'],
			'recursive' => -1
		]);

		foreach ($settingGroup['settings'] as $setting) {
			if (isset($settings[$setting['variable']])) {
				$this->request->data['Setting'][$setting['variable']] = $settings[$setting['variable']];
			}
		}
	}

	public function logs($type = "error")
	{
		$this->Ajax->initModal('normal', __('Logs') . ' (' . $type . ')');
        $this->Modals->addFooterButton(__('Delete Error Log File'), [
            'class' => 'btn btn-danger',
            'data-yjs-request' => 'app/load',
            'data-yjs-event-on' => 'click',
            'data-yjs-datasource-url' => Router::url([
				'controller' => 'settings',
				'action' => 'deleteLogs',
				$type
			]),
            'data-yjs-target' => 'modal',
            'data-yjs-modal-id' => null
        ]);
        $this->Modals->addFooterButton(__('Download Error Log File'), [
            'class' => 'btn btn-primary',
            'href' => Router::url([
                'controller' => 'settings',
                'action' => 'downloadLogs',
                $type
            ])
        ], null, true, 'a');

        $this->Modals->addFooterButton(__('Download All Log Files'), [
            'class' => 'btn btn-primary',
            'href' => Router::url([
                'controller' => 'settings',
                'action' => 'downloadLogs'
            ])
        ], null, true, 'a');

		$this->set('type', $type);

		$fileName = $type.".log";
		$file = new File( APP. "tmp/logs/" . $fileName);

		$logsLimit = 1000;

		$errorArr = array();

		$state = 'success';

		if($file->exists()){
			if($type == "error"){
				$fileArr = preg_split("/((\r?\n)|(\r\n?))/", $file->read());
				if(!empty($fileArr)){
					foreach ($fileArr as $line) {
						if ($logsLimit <= 0) {
                            break;
                        }
						if(strpos($line, 'Error:')){
							$explode = explode('Error:', $line, 2);
							if (count($explode) == 2 && strtotime($explode[0]) !== false) {
								$errorArr[] = $explode;
								$logsLimit--;
							}
						}
					}
					$errorArr = array_reverse($errorArr);
				}
			}
			elseif($type == "email"){
				$fileArr = preg_split("/((\r?\n)|(\r\n?))/", trim($file->read()));

				if(!empty($fileArr)){
					foreach ($fileArr as $lineNum => $line) {
						if ($logsLimit <= 0) {
                            break;
                        }
						if(strpos($line, 'Email:')){
							$explode = (explode('Email:', $line));

							// read CakeEmail log entry for email that was sent
							if (!trim($explode[1])) {
								$whiteList = array('To:', 'Subject:');

								for ($i=$lineNum+1; $i < $lineNum+10; $i++) { 
									if (isset($fileArr[$i])) {
										foreach ($whiteList as $param) {
											if (strpos($fileArr[$i], $param) !== false) {
												$explode[1] .= $fileArr[$i] . PHP_EOL;
											}
										}
									}
								}

								$explode[1] = trim($explode[1]);
								if (!empty($explode[1])) {
									$errorArr[] = $explode;
									$logsLimit--;
								}
							}
							// read EmailDebug entry
							else {
								$errorArr[] = explode('Email:', $line);
								$logsLimit--;
							}
						}
					}
					$errorArr = array_reverse($errorArr);
				}
			}
		}

		$this->YoonityJSConnector->setState($state);

		$this->set('errorArr', $errorArr);
	}

	public function deleteLogs($type = "error")
	{
		$fileName = $type.".log";
		$file = new File( APP. "tmp/logs/" . $fileName);
		if($file->delete()){
			$this->Flash->success(__('Log file was deleted'));
		}
		else{
			$this->Flash->error(__('Log file can not be deleted'));
		}

		return $this->redirect(['action' => 'logs', $type]);
	}

	public function downloadLogs($type = null)
	{
		$files = [];
		$logTypes = (!empty($type)) ? [$type] : CakeLog::configured();

		foreach ($logTypes as $logType) {
			$File = $this->_getLogFile($logType);

			if (!empty($File)) {
				$files[] = $File;
			}
		}

		$this->autoRender = false;

		if (empty($files)) {
			$this->Flash->error(__('Log file does not exist or is not readable'));
			return $this->redirect(['controller' => 'Settings', 'action' => 'index']);
		}

		if ($type !== null) {
			$this->response->file(
				$files[0]->path,
				[
					'download' => true,
					'name' => $type . '_' . date('Y-m-d') . '.log'
				]
			);
		}
		else {
			$archiveName = "logs" . time() . ".zip";
			$archiveFullPath = TMP . $archiveName;

			$zip = new ZipArchive();
			$res = $zip->open($archiveFullPath, ZipArchive::CREATE);

			if ($res !== true) {
				$this->Flash->error(__('Logs archive cannot be created'));
				return $this->redirect(['controller' => 'Settings', 'action' => 'index']);
			}

			foreach ($files as $LogFile) {
				$zip->addFile($LogFile->path, $LogFile->name);
			}

			$zip->close();

			$ZipFile = new File($archiveFullPath);
			$zipRespone = $ZipFile->read();
			$ZipFile->delete();

			$this->response->download('logs_' . date('Y-m-d') . '.zip');
			$this->response->body($zipRespone);
		}
    }

  	protected function _getLogFile($type)
  	{
  		if (!in_array($type, CakeLog::configured()) || empty(CakeLog::stream($type)) || !(CakeLog::stream($type) instanceof FileLog)) {
  			return false;
  		}

  		$config = CakeLog::stream($type)->config();

  		$fileName = $config['file'];
  		if (strpos($fileName, '.log') === false) {
  			$fileName .= '.log';
  		}

  		$LogFile = new File($config['path'] . $fileName);

  		if (!$LogFile->exists() || !$LogFile->readable()) {
  			return false;
  		}

  		return $LogFile;
  	}

	public function zipErrorLogFiles($fileDateTime = null)
	{
		if (!empty($fileDateTime) && $this->Auth->user('id') != ADMIN_ID) {
			throw new UnauthorizedException(__('You don\'t have an access to this location.'));
		}

		// Save actual datetime so both files will use the same datetime
		$actualDateTime = date('YmdHis', time());

		$zipFileName = 'error-log-file-' . $actualDateTime . '-' . md5($actualDateTime . mt_rand(5, 10));
		$zipFileExt = '.zip';
		$zipFileFullPath = APP . 'webroot/error-files/';
		$zipFileWebPath = '/error-files/';

		//
		// Get additional error info from the form
		$errorInfoFile = null;
		if (!empty($fileDateTime)) {
			$errorInfoFileTemp = new File(APP . 'webroot/error-files/error-info-' . $fileDateTime . '.html');
			if ($errorInfoFileTemp->exists()) {
				$errorInfoFile = $errorInfoFileTemp;
			}
		} else {
			$errorInfo = [];
			// Add error info from form
			foreach ($this->request->data as $key => $val) {
				if (strpos($key, 'error-') === 0) {
					$errorInfo[$key] = $val;
				}
			}

			if (!empty($errorInfo)) {
				$errorInfo = array_merge([
					// Add user ID
					'error-user' => $this->Auth->user('full_name') . ' (' . $this->Auth->user('email') . ')',
					// Add link for admin user
					'error-link' => Router::url([
						'controller' => 'settings',
						'action' => 'zipErrorLogFiles',
						$actualDateTime
					], true)
				], $errorInfo);
				$eInfoFileName = 'error-info-' . $actualDateTime . '.html';
				$errorInfoFile = new File(APP . 'webroot/error-files/' . $eInfoFileName, true);
				// Add all error info to error info file
				foreach ($errorInfo as $key => $val) {
					$heading = "<h3 style=\"margin-top: 30px;\">" . ucfirst(substr($key, 6)) . ":</h3>\n";
					$content = "";
					if ($key === 'error-link') {
						$content = "<a href=\"" . $val . "\">" . __("Download all log files (Admin only)") . "</a>\n\n";
					} else {
						$content = "<div>" . $val . "</div>\n\n";
					}
					$errorInfoFile->append($heading . $content);
				}
			}
		}
		//

		$dir = new Folder(APP . 'tmp/logs/');
		$logFiles = $dir->find('.*\.log');

		$zipFile = $zipFileFullPath . $zipFileName . $zipFileExt;
		$zip = new ZipArchive;
		$zip->open($zipFile, ZipArchive::CREATE);

		//
		// If user is admin, add all log files to zip file
		if ($this->Auth->user('id') == ADMIN_ID) {
			foreach ($logFiles as $logFile) {
		    	$zip->addFile(APP . 'tmp/logs/' . $logFile, $logFile);
		    }
		}
		//

	    // Add error-info file to zip file
	    if (!empty($errorInfoFile)) {
	    	$zip->addFile($errorInfoFile->path, $errorInfoFile->name);
	    }

	    $zipNumFiles = $zip->numFiles;
	    $zip->close();

	    if ($zipNumFiles == 0) {
	    	$tempZipFile = new File($zipFileFullPath . $zipFileName . $zipFileExt);
	    	if ($tempZipFile->exists()) {
	    		$tempZipFile->delete();
	    	}

	    	throw new NotFoundException(__('No error info data found.'));
	    } else {
	    	header("Content-Type: application/zip");
		    header("Content-Disposition: attachment; filename=" . $zipFileName . $zipFileExt);
		    header('Content-Length: ' . filesize($zipFile));
		    header("Location: " . $zipFileWebPath . $zipFileName . $zipFileExt);
	    }

	    //
	    // Delete all files except the last 5
	    $dir = new Folder(APP . 'webroot/error-files/');
	    $oldHtmlFiles = $dir->find('.*\.html');
		$oldZipFiles = $dir->find('.*\.zip');
		$allOldFiles = [$oldHtmlFiles, $oldZipFiles];
		foreach ($allOldFiles as $oldFiles) {
			if (count($oldFiles) > 5) {
				for ($i = 0; $i < count($oldFiles) && $i < count($oldFiles) - 5; ++$i) {
					$file = new File(APP . 'webroot/error-files/' . $oldFiles[$i]);
					$file->delete();
				}
			}
		}
		//
		
	    exit;
	}

    /**
     * Action outputs a custom logo uploaded to be used in the app. Direct img routing is disabled.
     */
    public function getLogo($htmlTag = false)
    {
    	$customLogo = Configure::read('Eramba.Settings.CUSTOM_LOGO');
    	if (!$htmlTag && !empty($customLogo)) {
    		$this->response->file('webroot' . $customLogo, array(
				'download' => true,
				'name' => basename($customLogo)
			));

			return $this->response;
    	}
    }

	private function manageBeforeSaveCallbacks($key, $value){
		$ret = true;

		if ($key === 'PDF_PATH_TO_BIN') {
			if (strpos(strtolower($value), 'phar://') !== false) {
				$ret = false;
			}
		}

		return $ret;
	}

	private function manageCallbacks($key){
		$ret = true;

		//validation
		if (in_array($key, array_keys($this->Setting->validate))) {
			$this->Setting->set([$key => $this->request->data['Setting'][$key]]);
			$ret = $this->Setting->validates(array('fieldList' => [$key]));
		}

		if ($ret && $key == 'PDF_PATH_TO_BIN' && !empty($this->request->data['test_download'])) {
			$this->redirect(Router::url(['plugin' => null, 'controller' => 'settings', 'action' => 'downloadTestPdf', '?' => ['path' => $this->request->data['Setting'][$key]]]));
		}

		return $ret;
	}

	public function testMailConnection()
	{
		$settingsFormName = 'Setting';
		$testMailFormName = 'testMailForm';

		$this->Ajax->initModal('normal', __('Test email connection'));
		$this->Modals->addFooterButton(__('Test'), [
			'class' => 'btn btn-primary',
			'data-yjs-request' => 'crud/submitForm',
			'data-yjs-event-on' => 'click',
			'data-yjs-forms' => $settingsFormName . '|' . $testMailFormName,
			'data-yjs-datasource-url' => '/settings/testMailConnection',
			'data-yjs-modal-id' => null,
			'data-yjs-target' => 'modal'
		]);

		if ($this->request->is(['post', 'put'])) {
			$state = 'error';
			$emailSettings = $this->request->data;
			$to = !empty($emailSettings[$testMailFormName]['email']) ? $emailSettings[$testMailFormName]['email'] : null;

			if(!empty($emailSettings) && Validation::email($to)){
				$config = $emailSettings[$settingsFormName];
				$config = ErambaCakeEmail::buildErambaConfig($config, true);
				
				$email = new ErambaCakeEmail($config);
				$email->to($to);
				$email->subject(__('Test Email from Eramba'));
				$email->template('test');

				try {
				    if ($email->send()) {
						$state = 'success';
					}
					else{
						$state = 'error';
					}
				} catch (Exception $e) {
				    $state = 'error';
				}
			}
			else{
				$state = 'error';
			}

			$this->YoonityJSConnector->setState($state);

			if ($state === 'success') {
				$this->Flash->success(__('Worked, we sent you an email.'));
			} else if ($state === 'error') {
				$this->Flash->error(__('There is a problem with your email connection.'));
			}
		}

		$this->set(compact('testMailFormName'));
	}

	public function resetDashboards(){
		$this->set('title_for_layout', 'Dashboard');
		$this->set('subtitle_for_layout', 'Reset Dashboards');

		$allowedResets = array(
			'AwarenessOvertimeGraph' => __('Awareness Overtime'),
			'ComplianceAuditOvertimeGraph' => __('Compliance Audit Overtime'),
			'ProjectOvertimeGraph' => __('Project Overtime'),
			'RiskOvertimeGraph' => __('Risk Overtime'),
			'ThirdPartyAuditOvertimeGraph' => __('Third Party Audit Overtime'),
			'ThirdPartyIncidentOvertimeGraph' => __('Third Party Incident Overtime'),
			'ThirdPartyOvertimeGraph' => __('Third Party Overtime'),
			'ThirdPartyRiskOvertimeGraph' => __('Third Party Risk Overtime')
		);

		$this->set('allowedResets', $allowedResets);

		$rules = array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Select date'
			),
			'date' => array(
				'rule' => 'date',
				'message' => 'Invalid format'
			)
		);

		if ($this->request->is(array('post', 'put'))) {

			$this->Setting->validate['to'] = $rules;
			if (!isset($this->request->data['Setting']['from_beginning'])) {
				$this->Setting->validate['from'] = $rules;
			}

			if ($this->Setting->saveAll($this->request->data, array('validate' => 'only'))) {

				$this->Setting->query('SET autocommit = 0');
				$this->Setting->begin();
				$ret = true;

				foreach ($this->request->data['Model'] as $model => $value) {
					$conditions = array();
					$this->request->data['Setting']['to']++;
					$conditions[$model.'.created <='] = $this->request->data['Setting']['to'];
					if (isset($this->request->data['Setting']['from'])) {
						$conditions[$model.'.created >='] = $this->request->data['Setting']['from'];
					}

					if (isset($allowedResets[$model])) {
						$this->loadModel($model);
						$ret &= $this->{$model}->deleteAll($conditions);
					}
				}

				if ($ret) {
					$this->Setting->commit();
					$this->Session->setFlash(__('Dashboards successfully reseted.'), FLASH_OK);
					$this->request->data = array();
					//$this->redirect(array('controller' => 'settings', 'action' => 'index'));
				}
				else {
					$this->Setting->rollback();
					$this->Session->setFlash( __( 'Error while reseting data. Please try it again.' ), FLASH_ERROR );
				}
			}
		}
	}


	/**
	 * Upload a custom logo for this application instance.
	 */
	public function customLogo()
	{
		$clFormName = 'Setting';

        $this->Ajax->initModal('normal', __('Logo'));

        if (!empty(Configure::read('Eramba.Settings.CUSTOM_LOGO'))) {
        	$this->Modals->addFooterButton(__('Delete'), [
	        	'class' => 'btn btn-danger',
	        	'data-yjs-request' => 'app/load',
	        	'data-yjs-event-on' => "click",
			    'data-yjs-datasource-url' => Router::url([
					'action' => 'customLogo',
					'?' => [
						'delete' => true
					]
				]),
				'data-yjs-target' => "modal",
				'data-yjs-modal-id' => null,
				'data-yjs-on-modal-success' => 'close',
				'data-yjs-on-success' => '#header-logo'
			]);
        }

        $this->Modals->addFooterButton(__('Change'), [
            'class' => 'btn btn-primary',
            'data-yjs-request' => 'crud/load',
            'data-yjs-event-on' => 'click',
            'data-yjs-server-url' => 'post::' . Router::url(['controller' => 'settings', 'action' => 'customLogo']),
            'data-yjs-forms' => $clFormName,
            'data-yjs-target' => 'modal',
            'data-yjs-modal-id' => null,
            'data-yjs-on-modal-success' => 'close',
            'data-yjs-on-success' => '#header-logo'
        ]);

		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.variable' => 'CUSTOM_LOGO'
			),
			'fields' => array(
				'Setting.id'
			)
		));

		if(isset($this->request->query['delete'])) {
			$state = 'error';
			if (!empty($setting['Setting']['id'])) {
				$this->Setting->delete($setting['Setting']['id']);
				// $this->Flash->success(__( 'Logo was successfully deleted.'));

				$state = 'success';
			}

			$this->YoonityJSConnector->setState($state);
		} else if ($this->request->is(['post', 'put'])) {
			$state = 'error';
			unset($this->request->data[$clFormName]['id']);

			if (isset($this->request->data[$clFormName]['logo_file'])) {
				if (empty($setting)) {
					$this->request->data[$clFormName]['name'] = 'Custom Logo';
					$this->request->data[$clFormName]['variable'] = 'CUSTOM_LOGO';
				} else {
					$this->request->data[$clFormName]['id'] = $setting['Setting']['id'];
				}

				$this->Setting->set($this->request->data);
				$ret = $this->Setting->save();

				if ($ret) {
					// $this->Flash->success( __( 'Logo was successfully changed.' ));

					//
					// Set new CUSTOM LOGO to configure class
					$newSetting = $this->Setting->find('first', array(
						'conditions' => array(
							'Setting.variable' => 'CUSTOM_LOGO'
						),
						'fields' => array(
							'Setting.id',
							'Setting.value'
						)
					));

					Configure::write('Eramba.Settings.CUSTOM_LOGO', $newSetting['Setting']['value']);
					//
					
					$state = 'success';
				} else {
					$this->Flash->error( __( 'Error while saving the data. Please try it again.' ));
					$state = 'error';
				}
			} else {
				$this->Flash->error(__('You need to choose file for upload.'));
			}

			$this->YoonityJSConnector->setState($state);
		}

		$this->set(compact('clFormName'));
	}

	public function deleteCache($folder = ''){
		$this->autoRender = false;

		$ret = $this->Setting->deleteCache($folder);
		if($ret){
			$this->Session->setFlash(__('Cache successfully deleted.'), FLASH_OK);
		}
		else{
			$this->Session->setFlash(__('Cache was deleted but some files might have remained.'), FLASH_WARNING);
		}

		$this->redirect(array('controller' => 'settings', 'action' => 'index'));
	}

	public function resetDatabase()
	{
		$rdFormName = 'ResetDatabaseForm';

		$this->Ajax->initModal('normal', __('Reset Database'));
        $this->Modals->addFooterButton(__('Reset'), [
            'class' => 'btn btn-danger',
            'data-yjs-request' => 'app/submitForm',
            'data-yjs-event-on' => 'click',
            'data-yjs-datasource-url' => Router::url(['controller' => 'settings', 'action' => 'resetDatabase']),
            'data-yjs-forms' => $rdFormName,
            'data-yjs-target' => 'modal',
            'data-yjs-modal-id' => null,
            'data-yjs-on-modal-success' => 'close'
        ]);

		if ($this->request->is(array('post', 'put'))) {
			$state = 'error';
			if ($this->request->data[$rdFormName]['reset_db']) {
				$dataSource = $this->Setting->getDataSource();
				$dataSource->begin();

				$ret = $this->Setting->resetDatabase(true);

				if($ret){
					$dataSource->commit();
					$this->Session->setFlash(__('Database succesfully reseted.'), FLASH_OK);

					$state = 'success';
				}
				else{
					$dataSource->rollback();
					$this->Session->setFlash(__('Unable to reset database'), FLASH_ERROR);

					$state = 'error';
				}
			}
			else {
				$this->Session->setFlash(__('Check the Reset database checkbox first.'), FLASH_WARNING);

				$state = 'error';
			}

			$this->YoonityJSConnector->setState($state);
		}

		$this->set(compact('rdFormName'));
	}

	public function systemHealth()
	{
		$this->Ajax->initModal('normal', __('System Health'));

		$SystemHealthLib = new SystemHealthLib();
		$data = $SystemHealthLib->getData();

		$AutoUpdateLib = new AutoUpdateLib();

		$this->set('autoUpdatePending', $AutoUpdateLib->hasPending());
		$this->set('data', $data);
	}

	public function getTimeByTimezone()
	{
		$timezone = null;
		if (!empty($this->request->data['Setting']['TIMEZONE'])) {
			$timezone = $this->request->data['Setting']['TIMEZONE'];
		}

		$dateTime = CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('now', $timezone));;

		$this->set('dateTime', getEmptyValue($dateTime));
	}

	public function _beforeResidualRiskHandle(CakeEvent $e)
	{
		$_Collection = new FieldDataCollection([], $e->subject->model);

		$_Collection->add('value', [
			'type' => 'select',
			'label' => __('Granularity'),
			'editable' => true,
			'description' => __('When you create a risk and set a residual score you do that as a percentage of the total Risk score. This option allows you to set the scales uses for the percentage value, by default the value is 10.'),
			// 'empty' => __('Choose one ...'),
			'options' => [
				1 => 1,
				2 => 2,
				5 => 5,
				10 => 10
			],
			'defualt' => 10
		]);

		$this->_FieldDataCollection = $_Collection;
	}

	public function residualRisk() {
		$data = $this->Setting->find('first', [
			'conditions' => [
				'Setting.variable' => 'RISK_GRANULARITY'
			],
			'recursive' => -1
		]);

		$this->Crud->on('beforeHandle', [$this, '_beforeResidualRiskHandle']);

		return $this->Crud->execute(null, [$data['Setting']['id']]);


		$data = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.variable' => 'RISK_GRANULARITY'
			),
			'recursive' => -1
		));

		if ( empty( $data ) ) {
			throw new NotFoundException();
		}

		$this->Ajax->processEdit($data['Setting']['id']);

		$this->set( 'title_for_layout', __( 'Residual Risk Settings' ) );
		$this->set('modalPadding', true);

		if ($this->request->is(array('post', 'put'))) {
			unset($this->request->data['Setting']['id']);

			$this->request->data['Setting']['id'] = $data['Setting']['id'];

			// $ret &= $this->Setting->updateVariable($key, $value);

			$this->Setting->set($this->request->data);
			$ret = $this->Setting->save();
			if($ret){
				$this->Ajax->success();
				$this->Session->setFlash( __( 'Risk configuration was successfully updated.' ), FLASH_OK );
			}
			else {
				$this->Session->setFlash( __( 'Error while saving the data. Please try it again.' ), FLASH_ERROR );
			}
		}
		else {
			$this->request->data = $data;
		}
	}

	public function testPdf()
	{
		$this->layout = 'test_pdf';

		//turn off debug mode
		Configure::write('debug', 0);
	}

	public function downloadTestPdf()
	{
		$path = $this->request->query['path'];

		$Pdf = new \Knp\Snappy\Pdf($path);

		$Pdf->setOptions([
			'orientation' => 'portrait',
			'dpi' => 100,
		]);

		$url = Router::url(['plugin' => null, 'controller' => 'settings', 'action' => 'testPdf'], true);

		$this->autoRender = false;

		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="test.pdf"');

		return $Pdf->getOutput($url);
	}

}