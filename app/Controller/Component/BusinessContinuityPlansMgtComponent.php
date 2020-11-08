<?php
App::uses('Component', 'Controller');
App::uses('ErambaCakeEmail', 'Network/Email');

class BusinessContinuityPlansMgtComponent extends Component {

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 * BCM awareness role reminders.
	 */
	public function cron() {
		$this->controller->loadModel( 'BusinessContinuityPlan' );
		$data = $this->controller->BusinessContinuityPlan->find( 'all', array(
			'fields' => array( 'id', 'awareness_recurrence' ),
			'contain' => array(
				'BusinessContinuityTask' => array(
					'fields' => array( 'id', 'awareness_role' ),
					'BusinessContinuityTaskReminder' => array(
						'limit' => 1,
						'order' => 'BusinessContinuityTaskReminder.created DESC'
					)
				)
			)
		) );

		$ret = true;
		foreach ( $data as $bcp ) {
			if (!empty($bcp['BusinessContinuityPlan']['awareness_recurrence'])) {
				$days = $this->getDaysFromString( $bcp['BusinessContinuityPlan']['awareness_recurrence'] );
				foreach ( $bcp['BusinessContinuityTask'] as $bct ) {
					if ( $bct['awareness_role'] == null ) {
						continue;
					}

					if ( empty( $bct['BusinessContinuityTaskReminder'] ) ) {
						$ret &= $this->sendReminder( $bct['awareness_role'], $bct['id'] );
						continue;
					}

					$datetime = CakeTime::format( 'Y-m-d', CakeTime::fromString( '-' . $days . ' days' ) );
					$bct_datetime = CakeTime::format( 'Y-m-d', CakeTime::fromString( $bct['BusinessContinuityTaskReminder'][0]['created'] ) );
					if ( $bct_datetime < $datetime ) {
						$ret &= $this->sendReminder( $bct['awareness_role'], $bct['id'] );
					}
				}
			}
		}

		return $ret;
	}

	private function sendReminder( $user_id = null, $bct_id = null ) {
		$this->controller->loadModel( 'User' );
		$user = $this->controller->User->find( 'first', array(
			'User.id' => $user_id,
			'fields' => array( 'email'),
			'recursive' => -1
		) );

		$this->controller->loadModel( 'BusinessContinuityTaskReminder' );
		$this->controller->BusinessContinuityTaskReminder->create();
		$save = $this->controller->BusinessContinuityTaskReminder->save( array(
			'business_continuity_task_id' => $bct_id,
			'user_id' => $user_id
		) );

		$ret = true;
		if ( $save ) {
			$bctr_id = $this->controller->BusinessContinuityTaskReminder->id;

			$emailData = array(
				'url' => Router::url( array( 'plugin' => null, 'controller' => 'businessContinuityPlans', 'action' => 'acknowledge', $bct_id, $bctr_id ), true )
			);

			$email = new ErambaCakeEmail('default');
			$email->to($user['User']['email']);
			$email->subject(__( 'Business Continuity Task Review' ));
			$email->template('business_continuity_task');
			$email->viewVars($emailData);

			$ret &= $email->send();
		}

		return $ret;
	}

	private function getDaysFromString($str = null) {
		switch ($str) {
			case 'monthly':
				return 30;
				break;
			case 'quarterly':
				return 120;
				break;
			case 'semester':
				return 180;
				break;
			case 'yearly':
				return 365;
				break;
		}
	}

}
