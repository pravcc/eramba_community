<?php
App::uses('Component', 'Controller');
App::uses('AppAclComponent', 'Controller/Component');

class MenuComponent extends Component {
	public $components = array('AppAcl', 'Auth');

	public function startup(Controller $controller) {
		$this->controller = $controller;
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 * Define here all possible actions for the menu
	 *
	 * WHEN YOU ADD NEW ITEMS, PLEASE DELETE ALL ACL CACHE FILES FOR MENU
	 */
	private function getActions() {
		return array(
			array(
				'name' => __('Dashboard'),
				'icon' => 'icon-chart',
				'class' => 'menu-dashboard',
				'url' => $this->Auth->loginRedirect,
				'childSections' => [
					'dashboardKpis'
				]
			),
			array(
				'name' => __('Program'),
				'class' => 'menu-program',
				'children' => array(
					array(
						'name' => __('Scope'),
						'url' => array(
							'controller' => 'programScopes', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Issues'),
						'url' => array(
							'controller' => 'programIssues', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Goals'),
						'url' => array(
							'controller' => 'goals', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'goalAudits'
						]
					),
					array(
						'name' => __('Team Roles'),
						'url' => array(
							'controller' => 'teamRoles', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
				)
			),
			array(
				'name' => __('Organization'),
				'icon' => 'icon-users4',
				'class' => 'menu-organization',
				'children' => array(
					array(
						'name' => __('Business Units'),
						'url' => array(
							'controller' => 'businessUnits', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'processes'
						]
					),
					array(
						'name' => __('Liabilities'),
						'url' => array(
							'controller' => 'legals', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Third Parties'),
						'url' => array(
							'controller' => 'thirdParties', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					)
				)
			),
			array(
				'name' => __('Asset Mgt'),
				'icon' => 'icon-home2',
				'children' => array(
					array(
						'name' => __('Asset Identification'),
						'url' => array(
							'controller' => 'assets', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'assetReviews'
						]
					),
					array(
						'name' => __('Data Flow Analysis'),
						'url' => array(
							'controller' => 'dataAssetInstances', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'dataAssets'
						]
					),
				)
			),
			array(
				'name' => __('Controls Catalogue'),
				'class' => 'menu-controls',
				'children' => array(
					array(
						'name' => __('Internal Controls'),
						'url' => array(
							'controller' => 'securityServices', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'securityServiceAudits',
							'securityServiceMaintenances',
							'securityServiceIssues'
						]
					),
					array(
						'name' => __('Support Contracts'),
						'url' => array(
							'controller' => 'serviceContracts', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Business Continuity Plans'),
						'url' => array(
							'controller' => 'businessContinuityPlans', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'businessContinuityPlanAudits',
							'businessContinuityTasks'
						]
					),
					array(
						'name' => __('Security Policies'),
						'url' => array(
							'controller' => 'securityPolicies', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'securityPolicyReviews'
						]
					),
					array(
						'name' => __('Policy Exceptions'),
						'url' => array(
							'controller' => 'policyExceptions', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					)
				)
			),
			array(
				'name' => __('Risk Mgt'),
				'icon' => 'icon-unlink2',
				'class' => 'menu-risk',
				'children' => array(
					array(
						'name' => __('Asset Risk Management'),
						'url' => array(
							'controller' => 'risks', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'riskReviews'
						]
					),
					array(
						'name' => __('Risk Exceptions'),
						'url' => array(
							'controller' => 'riskExceptions', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Third Party Risk Management'),
						'url' => array(
							'controller' => 'thirdPartyRisks', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'thirdPartyRiskReviews'
						]
					),
					array(
						'name' => __('Business Impact Analysis'),
						'url' => array(
							'controller' => 'businessContinuities', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'businessContinuityReviews'
						]
					)
				)
			),
			array(
				'name' => __('Compliance Mgt'),
				'icon' => 'icon-clipboard5',
				'class' => 'menu-compliance',
				'children' => array(
					array(
						'name' => __('Compliance Exception'),
						'url' => array(
							'controller' => 'complianceExceptions', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Compliance Packages'),
						'url' => array(
							'controller' => 'compliancePackageRegulators', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Compliance Analysis'),
						'url' => array(
							'controller' => 'complianceManagements', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Compliance Analysis Findings'),
						'url' => array(
							'controller' => 'complianceAnalysisFindings', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('Online Assessments'),
						'url' => array(
							'controller' => 'vendorAssessments', 'action' => 'index', 'admin' => false, 'plugin' => 'vendor_assessments'
						),
						'childSections' => [
							'vendorAssessmentQuestions',
							'vendorAssessmentFindings',
							'vendorAssessmentSystemLogs'
						],
						'enterprise' => true
					)
				)
			),
			array(
				'name' => __('Security Operations'),
				'class' => 'menu-security',
				'children' => array(
					array(
						'name' => __('Project Management'),
						'url' => array(
							'controller' => 'projects', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'projectAchievements',
							'projectExpenses'
						]
					),
					array(
						'name' => __('Security Incidents'),
						'url' => array(
							'controller' => 'securityIncidents', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'securityIncidentStagesSecurityIncidents'
						]
					),
					array(
						'name' => __('Awareness Programs'),
						'url' => array(
							'controller' => 'awarenessPrograms', 'action' => 'index', 'admin' => false, 'plugin' => null
						),
						'childSections' => [
							'awarenessProgramActiveUsers',
							'awarenessProgramCompliantUsers',
							'awarenessProgramIgnoredUsers',
							'awarenessProgramNotCompliantUsers',
							'awarenessReminders'
						],
						'enterprise' => true
					),
					array(
						'name' => __('Account Reviews'),
						'url' => array(
							'controller' => 'accountReviews', 'action' => 'index', 'admin' => false, 'plugin' => 'account_reviews'
						),
						'childSections' => [
							'accountReviewPulls',
							'accountReviewFeedbacks',
							'accountReviewFindings',
							'accountReviewPullSystemLogs'
						],
						'enterprise' => true
					)
				)
			),
			array(
				'name' => __('System'),
				'icon' => 'icon-accessibility',
				'class' => 'menu-system',
				'children' => array(
					// array(
					// 	'name' => __('System Records'),
					// 	'url' => array(
					// 		'controller' => 'systemRecords', 'action' => 'index', 'admin' => false, 'plugin' => null
					// 	)
					// ),
					array(
						'name' => __('Settings'),
						'url' => array(
							'controller' => 'settings', 'action' => 'index', 'admin' => false, 'plugin' => null
						)
					),
					array(
						'name' => __('About'),
						'url' => array(
							'controller' => 'pages', 'action' => 'about', 'admin' => false, 'plugin' => null
						)
					)
				)
			)
		);
	}

	/**
	 * Return menu based on user rights
	 *
	 * @param array $groups users group ids
	 */
	public function getMenu($groups, $lang = 'eng')
	{
		$groupsTemp = $groups;
		sort($groupsTemp, SORT_NUMERIC);
		$groupsString = implode('-', $groupsTemp);
		if (($userActions = Cache::read('menu_items_'. $groupsString . '_' . $lang, 'acl')) === false) {

			$userActions = $this->checkMenuItems($groups);

			Cache::write('menu_items_'. $groupsString . '_' . $lang, $userActions, 'acl');
		}
		
		return $userActions;
	}

	/**
	 * Check the menu based on unser rights in acl
	 *
	 * @param array $groups users group ids
	 */
	private function checkMenuItems($groups) {
		$actions = $this->getActions();
		$userActions = array();

		foreach ($actions as $section) {
			
			if (empty($section['children'])) {
				$hasRights = $this->checkSingleAction($section, $groups);
				if ($hasRights) {
					$userActions[] = $section;
				}
			}
			else {
				$localActions = array();

				//check rights for each children
				foreach ($section['children'] as $action) {
					$hasRights = $this->checkSingleAction($action, $groups);

					//if user has the right for the action include it
					if ($hasRights) {
						$localActions[] = $action;
					} else {
						// in case user has no access to the given action and is part of enterprise, we put it into the menu
						// with enterprise label
						if (!empty($action['enterprise'])) {
							$action['url'] = 'https://www.eramba.org/services';
							$action['forceEnterprise'] = true;
							$localActions[] = $action;
						}
					}
				}

				if (!empty($localActions)) {
					unset($section['children']);

					$localSection = $section;
					$localSection['children'] = $localActions;

					$userActions[] = $localSection;
				}
			}

			
		}

		return $userActions;
	}

	private function checkSingleAction($action, $groups) {
		$actionPrefix = '';
		$adminPrefixes = Configure::read('Routing.prefixes');

		//check if the url contains admin prefixes - if so include it to action as prefix
		if (is_array($adminPrefixes)) {
			foreach ($adminPrefixes as $prefix) {
				if (isset($action['url'][$prefix]) && $action['url'][$prefix]) {
					$actionPrefix = $prefix . '_';
					break;
				}
			}
		}
		elseif (!empty($adminPrefixes)) {
			if (isset($action['url'][$adminPrefixes]) && $action['url'][$adminPrefixes]) {
				$actionPrefix = $adminPrefixes . '_';
				// break;
			}
		}

		//check the rights
		$hasRights = $this->AppAcl->check($action['url'], $groups);

		return $hasRights;
	}
}
