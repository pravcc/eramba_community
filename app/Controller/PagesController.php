<?php
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {
	public $components = array('Feeds', 'AppAcl');
	public $helpers = ['Dashboard.Dashboard'];

	/**
	 * This controller does not use a model
	 *
	 * @var array
	 */
	public $uses = array();

	public function beforeFilter() {$this->Auth->authorize=false;
		if ($this->params['action'] == 'license') {
			$this->Components->disable('Acl');
			$this->Components->disable('Auth');
		}
		$this->Auth->allow('resetpassword', 'useticket', 'logout', 'welcome');

		parent::beforeFilter();

		// in case someone is not logged and access the '/' route which is welcome page (it has forced allow for ACL reasons) then redirect him to login page
		if ($this->params['action'] == 'welcome' && empty($this->logged)) {
			return $this->redirect($this->Auth->loginAction);
		}
	}

	/**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 * @return void
	 * @throws NotFoundException When the view file could not be found
	 *	or MissingViewException in debug mode.
	 */
	/*public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}*/

	/**
	 * Welcome page accessible for everyone.
	 */
	public function welcome() {
		return $this->redirect($this->Auth->loginRedirect);
	}

	/**
	 * Set up dashboard/home page.
	 */
	public function dashboard() {
		if (!isAdmin($this->logged)) {
			$this->Flash->error(__('You are not allowed to view admin dashboard as you are not in admin group.'));
			return $this->redirect(['plugin' => 'dashboard', 'controller' => 'dashboard_kpis', 'action' => 'user']);
		}

		$this->set( 'title_for_layout', __('Calendar') );
		$this->set( 'subtitle_for_layout', 'This is your calendar' );

		$this->set( 'feeds', $this->Feeds->getFeed() );
		$this->set( 'calendar_events', $this->getCalendarEvents() );
		// $this->set( 'hidePageHeader', true );

		$this->render('dashboard');
	}
	
	/**
	 * Summarize calendar events for several parts of the system.
	 */
	private function getCalendarEvents() {
		$fromAgo = CakeTime::format( 'Y-m-d', CakeTime::fromString( '-1 month' ) );

		$calendar_events = array();

		// risk exception expirations
		if ($this->AppAcl->check('/riskExceptions/index')) {
			$this->loadModel( 'RiskException' );
			$data = $this->RiskException->find( 'all', array(
				'conditions' => array(
					'RiskException.expiration >=' => $fromAgo
				),
				'fields' => array( 'id', 'title', 'expiration' ),
				'recursive' => -1
			) );

			foreach ( $data as $item ) {
				$calendar_events[] = array(
					'title' => __( 'Expiration' ) . ': ' . $item['RiskException']['title'],
					'start' => $this->toJsDateFormat( CakeTime::fromString( $item['RiskException']['expiration'] ) ),
					'backgroundColor' => COLOR_RISK
				);
			}
		}

		// risk reviews
		if ($this->AppAcl->check('/risks/index')) {
			$this->loadModel( 'Risk' );
			$data = $this->Risk->find( 'all', array(
				'conditions' => array(
					'Risk.review >=' => $fromAgo
				),
				'fields' => array( 'id', 'title', 'review' ),
				'recursive' => -1
			) );

			foreach ( $data as $item ) {
				$calendar_events[] = array(
					'title' => __( 'Review' ) . ': ' . $item['Risk']['title'],
					'start' => $this->toJsDateFormat( CakeTime::fromString( $item['Risk']['review'] ) ),
					'backgroundColor' => COLOR_RISK
				);
			}
		}

		// policy reviews
		if ($this->AppAcl->check('/securityPolicies/index')) {
			$this->loadModel( 'SecurityPolicy' );
			$data = $this->SecurityPolicy->find( 'all', array(
				'conditions' => array(
					'SecurityPolicy.next_review_date >=' => $fromAgo
				),
				'fields' => array( 'id', 'index', 'next_review_date' ),
				'recursive' => -1
			) );

			foreach ( $data as $item ) {
				$calendar_events[] = array(
					'title' => __( 'Review' ) . ': ' . $item['SecurityPolicy']['index'],
					'start' => $this->toJsDateFormat( CakeTime::fromString( $item['SecurityPolicy']['next_review_date'] ) ),
					'backgroundColor' => COLOR_CONTROLS
				);
			}
		}

		// policy exception expirations
		if ($this->AppAcl->check('/policyExceptions/index')) {
			$this->loadModel( 'PolicyException' );
			$data = $this->PolicyException->find( 'all', array(
				'conditions' => array(
					'PolicyException.expiration >=' => $fromAgo
				),
				'fields' => array( 'id', 'title', 'expiration' ),
				'recursive' => -1
			) );

			foreach ( $data as $item ) {
				$calendar_events[] = array(
					'title' => __( 'Expiration' ) . ': ' . $item['PolicyException']['title'],
					'start' => $this->toJsDateFormat( CakeTime::fromString( $item['PolicyException']['expiration'] ) ),
					'backgroundColor' => COLOR_CONTROLS
				);
			}
		}

		// service contract expirations
		if ($this->AppAcl->check('/serviceContracts/index')) {
			$this->loadModel( 'ServiceContract' );
			$data = $this->ServiceContract->find( 'all', array(
				'conditions' => array(
					'ServiceContract.end >=' => $fromAgo
				),
				'fields' => array( 'id', 'name', 'end' ),
				'recursive' => -1
			) );

			foreach ( $data as $item ) {
				$calendar_events[] = array(
					'title' => __( 'Expiration' ) . ': ' . $item['ServiceContract']['name'],
					'start' => $this->toJsDateFormat( CakeTime::fromString( $item['ServiceContract']['end'] ) ),
					'backgroundColor' => COLOR_CONTROLS
				);
			}
		}

		// security service audits
		if ($this->AppAcl->check('/securityServiceAudits/index')) {
			$this->loadModel( 'SecurityServiceAudit' );
			$data = $this->SecurityServiceAudit->find( 'all', array(
				'conditions' => array(
					'SecurityServiceAudit.planned_date >=' => $fromAgo
				),
				'fields' => array(
					'SecurityServiceAudit.id',
					'SecurityServiceAudit.planned_date',
					'SecurityService.name'
				),
				'recursive' => 0
			) );

			foreach ( $data as $item ) {
				$calendar_events[] = array(
					'title' => __( 'Audit' ) . ': ' . $item['SecurityService']['name'],
					'start' => $this->toJsDateFormat( CakeTime::fromString( $item['SecurityServiceAudit']['planned_date'] ) ),
					'backgroundColor' => COLOR_CONTROLS
				);
			}
		}

		// security service maintenances
		if ($this->AppAcl->check('/securityServiceMaintenances/index')) {
			$this->loadModel( 'SecurityServiceMaintenance' );
			$data = $this->SecurityServiceMaintenance->find( 'all', array(
				'conditions' => array(
					'SecurityServiceMaintenance.planned_date >=' => $fromAgo
				),
				'fields' => array(
					'SecurityServiceMaintenance.id',
					'SecurityServiceMaintenance.planned_date',
					'SecurityService.name'
				),
				'recursive' => 0
			) );

			foreach ( $data as $item ) {
				$calendar_events[] = array(
					'title' => __( 'Maintenance' ) . ': ' . $item['SecurityService']['name'],
					'start' => $this->toJsDateFormat( CakeTime::fromString( $item['SecurityServiceMaintenance']['planned_date'] ) ),
					'backgroundColor' => COLOR_CONTROLS
				);
			}
		}

		// project deadlines
		if ($this->AppAcl->check('/projects/index')) {
			$this->loadModel( 'Project' );
			$data = $this->Project->find( 'all', array(
				'conditions' => array(
					'Project.deadline >=' => $fromAgo
				),
				'fields' => array( 'id', 'title', 'deadline' ),
				'recursive' => -1
			) );

			foreach ( $data as $item ) {
				$calendar_events[] = array(
					'title' => __( 'Deadline' ) . ': ' . $item['Project']['title'],
					'start' => $this->toJsDateFormat( CakeTime::fromString( $item['Project']['deadline'] ) ),
					'backgroundColor' => COLOR_SECURITY
				);
			}
		}

		return $calendar_events;
	}

	public function about() {
		$this->set('title_for_layout', __('About'));
		$this->set('subtitle_for_layout', '');
	}

	public function license() {
		$this->set('title_for_layout', __('User License'));
		$this->layout = 'general';
	}

}