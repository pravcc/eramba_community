<?php
App::uses('AbstractUpdate', 'Lib');
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');
App::uses('CakeLog', 'Log');

class Update050 extends AbstractUpdate {

	/**
	 * After update process completes updating. That is after files are copied, database migrations has run successfully
	 * and new version values take place.
	 * 
	 * @return bool True to continue, False to break updating.
	 */
	public function run() {
		$ret = true;

		// set new session values for logged in user to pick up multiple groups feature
		$user = CakeSession::read(AuthComponent::$sessionKey);

		// removes the old group field
		$ret &= CakeSession::delete(AuthComponent::$sessionKey . '.Group');

		// sets up new groups field
		$ret &= CakeSession::write(AuthComponent::$sessionKey . '.Groups', [$user['group_id']]);

		if (!$ret) {
			CakeLog::write('error', __('Update 050 had issue at the end of the update process while trying to update current logged-in user\'s session to update group part for the new multiple groups feature.'));
		}

		return $ret;
	}

}