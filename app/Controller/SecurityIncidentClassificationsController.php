<?php
class SecurityIncidentClassificationsController extends AppController {
	public $helpers = array( 'Html', 'Form' );
	public $components = array( 'Session' );

	public function index() {
		$this->set( 'title_for_layout', __( 'Security Incident Classification Scheme' ) );
		$this->set( 'subtitle_for_layout', __( 'Apply this classification to your Security Incidents.' ) );

		$this->paginate = array(
			'conditions' => array(
			),
			'fields' => array(
				'SecurityIncidentClassification.id',
				'SecurityIncidentClassification.name',
				'SecurityIncidentClassification.criteria',
			),
			'order' => array('SecurityIncidentClassification.id' => 'ASC'),
			'limit' => $this->getPageLimit(),
			'recursive' => 0
		);

		$data = $this->paginate( 'SecurityIncidentClassification' );
		$this->set( 'data', $data );
	}

	public function delete($id = null) {
		$this->set('title_for_layout', __('Security Incident Classifications'));
		$this->set('subtitle_for_layout', __('Delete a Security Incident Classification.'));

		$data = $this->SecurityIncidentClassification->find('first', array(
			'conditions' => array(
				'SecurityIncidentClassification.id' => $id
			),
			'fields' => array('id', 'name'),
			'recursive' => -1
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SecurityIncidentClassification->delete($id)) {
				$this->Session->setFlash(__('Security Incident Classification was successfully deleted.'), FLASH_OK);
			}
			else {
				$this->Session->setFlash(__('Error while deleting the data. Please try it again.'), FLASH_ERROR);
			}

			$this->redirect(array('controller' => 'securityIncidentClassifications', 'action' => 'index'));
		}
		else {
			$this->request->data = $data;
		}
	}

	public function add() {
		$this->set( 'title_for_layout', __( 'Create a Security Incident Classification' ) );
		$this->initAddEditSubtitle();

		if ( $this->request->is( 'post' ) ) {
			unset( $this->request->data['SecurityIncidentClassification']['id'] );

			$this->SecurityIncidentClassification->set( $this->request->data );

			if ( $this->SecurityIncidentClassification->validates() ) {
				if ( $this->SecurityIncidentClassification->save() ) {
					$this->Session->setFlash( __( 'Security Incident Classification was successfully added.' ), FLASH_OK );
					$this->redirect( array( 'controller' => 'securityIncidentClassifications', 'action' => 'index' ) );
				} else {
					$this->Session->setFlash( __( 'Error while saving the data. Please try it again.' ), FLASH_ERROR );
				}
			} else {
				$this->Session->setFlash( __( 'One or more inputs you entered are invalid. Please try again.' ), FLASH_ERROR );
			}
		}
	}

	public function edit( $id = null ) {
		$id = (int) $id;

		if ( ! empty( $this->request->data ) ) {
			$id = (int) $this->request->data['SecurityIncidentClassification']['id'];
		}

		$data = $this->SecurityIncidentClassification->find( 'first', array(
			'conditions' => array(
				'SecurityIncidentClassification.id' => $id
			),
			'recursive' => -1
		) );

		if ( empty( $data ) ) {
			throw new NotFoundException();
		}

		$this->set( 'edit', true );
		$this->set( 'title_for_layout', __( 'Edit a Security Incident Classification' ) );
		$this->initAddEditSubtitle();

		if ( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

			$this->SecurityIncidentClassification->set( $this->request->data );

			if ( $this->SecurityIncidentClassification->validates() ) {
				if ( $this->SecurityIncidentClassification->save() ) {
					$this->Session->setFlash( __( 'Security Incident Classification was successfully edited.' ), FLASH_OK );
					$this->redirect( array( 'controller' => 'securityIncidentClassifications', 'action' => 'index', $id ) );
				}
				else {
					$this->Session->setFlash( __( 'Error while saving the data. Please try it again.' ), FLASH_ERROR );
				}
			} else {
				$this->Session->setFlash( __( 'One or more inputs you entered are invalid. Please try again.' ), FLASH_ERROR );
			}
		}
		else {
			$this->request->data = $data;
		}

		$this->render( 'add' );
	}

	private function initAddEditSubtitle() {
		$this->set( 'subtitle_for_layout', __( 'Define your Security Incident Classification.' ) );
	}

}
