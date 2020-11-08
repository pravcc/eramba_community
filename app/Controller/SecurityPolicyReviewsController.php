<?php
App::uses('ReviewsPlannerController', 'ReviewsPlanner.Controller');

/**
 * @section
 */
class SecurityPolicyReviewsController extends ReviewsPlannerController
{
	public $components = array(
		// reviews component handles correct model name configuration for CRUD
		'ObjectStatus.ObjectStatus',
		'Pdf',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'add' => [
					'enabled' => true
				],
				'edit' => [
					'enabled' => true
				],
				'delete' => [
					'enabled' => true
				],
				'trash' => [
					'enabled' => true
				],
				'history' => [
					'className' => 'ObjectVersion.History',
					'enabled' => true
				],
				'restore' => [
					'className' => 'ObjectVersion.Restore',
					'enabled' => true
				]
			],
			'listeners' => [
				'.SecurityPolicyReviewsPlanner'
			]
		],
	);

	public $uses = ['SecurityPolicyReview'];

	public function beforeFilter()
	{
		parent::beforeFilter();
		// $this->Crud->enable('add', 'edit', 'delete', 'index');
	}

	public function add($foreign_key = null)
	{
		$this->Crud->on('beforeRender', array($this, '_beforeAddRender'));

		return parent::add($foreign_key);
	}

	public function _beforeAddRender(CakeEvent $e)
	{
		$request = $e->subject->request;
		
		if ($request->is('get') && isset($request->data['SecurityPolicyReview']['foreign_key'])) {
			$policyId = $request->data['SecurityPolicyReview']['foreign_key'];
			$policy = ClassRegistry::init('SecurityPolicy')->find('first', [
				'conditions' => [
					'id' => $policyId
				],
				'recursive' => -1
			]);

			$request->data['SecurityPolicy'] = $policy['SecurityPolicy'];
		}
	}

	public function review($id)
	{
		$data = $this->SecurityPolicyReview->find('first', [
			'conditions' => [
				'SecurityPolicyReview.id' => $id
			]
		]);

		if (empty($data)) {
			throw new NotFoundException();
		}

		$reviews = ClassRegistry::init('SecurityPolicy')->getPolicyReviews($data['SecurityPolicy']['id']);
		$data['ReviewVersion'] = Hash::extract($reviews, '{n}.Review');

		$this->Modals->init();
		
		$reviewTitle = $data['SecurityPolicy']['index'];
		$this->Modals->setHeaderHeading($reviewTitle);

		$this->Modals->addFooterButton(__('Download PDF'), [
			'class' => 'btn btn-primary',
			'href' => Router::url(['admin' => false, 'plugin' => false, 'controller' => 'securityPolicyReviews', 'action' => 'reviewPdf', $id]),
		], 'downloadBtn');
		$this->Modals->changeConfig('footer.buttons.downloadBtn.tag', 'a');

		$this->set('review', $data);
		$this->set('reviewTitle', $reviewTitle);

		$this->render('review_wrapped');
	}

	public function reviewPdf($id)
	{
		$this->autoRender = false;

		$data = $this->SecurityPolicyReview->find('first', [
			'conditions' => [
				'SecurityPolicyReview.id' => $id
			]
		]);

		if (empty($data)) {
			throw new NotFoundException();
		}

		$reviews = ClassRegistry::init('SecurityPolicy')->getPolicyReviews($data['SecurityPolicy']['id']);
		$data['ReviewVersion'] = Hash::extract($reviews, '{n}.Review');

		$vars = [
			'review' => $data,
			'reviewTitle' => $data['SecurityPolicy']['index'],
			'pdf' => true
		];

		$name = Inflector::slug($data['SecurityPolicyReview']['planned_date'] . ' ' . $data['SecurityPolicy']['index'], '-');

		$this->Pdf->renderPdf($name, '..' . DS . 'SecurityPolicyReviews' . DS . 'review_wrapped', 'policy-external', $vars, true);
	}
}