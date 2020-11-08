<?php
App::uses('AbstractUpdate', 'Lib');
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');
App::uses('CakeLog', 'Log');

class Update051 extends AbstractUpdate {

	/**
	 * After update process completes updating. That is after files are copied, database migrations has run successfully
	 * and new version values take place.
	 * 
	 * @return bool True to continue, False to break updating.
	 */
	public function run() {
		$ret = true;

		$ret = ClassRegistry::init('Setting')->deleteCache(null);

		if (!$ret) {
			CakeLog::write('error', __('Update 051 couldnt delete a cache while finalizing an update.'));
		}

		return $ret;
	}

}