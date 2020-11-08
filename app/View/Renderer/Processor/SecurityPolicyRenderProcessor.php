<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('AbstractQuery', 'Lib.AdvancedFilters/Query');
App::uses('RelatedProjectStatusesTrait', 'View/Renderer/Processor/Trait');
App::uses('FieldsIterator', 'ItemData.Lib');
App::uses('SecurityPolicy', 'Model');
App::uses('Router', 'Routing');
App::uses('PolicyHelper', 'View/Helper');


class SecurityPolicyRenderProcessor extends SectionRenderProcessor
{
	use RelatedProjectStatusesTrait;
	
    public function objectStatusExpiredReviews(OutputBuilder $output, $subject)
    {
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityPolicyReviews', [
			'foreign_key' => $subject->item->getPrimary(),
			'ObjectStatus_expired' => 1
		]));
    }

    public function useAttachments(OutputBuilder $output, $subject)
    {
    	$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

        foreach ($FieldsIterator as $key => $value) {
			$output->label([
				$key => getPoliciesDocumentContentTypesWithoutUse()[$value['item']->use_attachments]
			]);

			$PolicyHelper = new PolicyHelper($subject->view);

			$viewUrl = $PolicyHelper->getDocumentUrl($value['item']->getPrimary(), true);
			$viewUrl['?']['from_app'] = true;

			$output->itemTemplate([
				$key => $subject->view->Html->link(OutputBuilder::CONTENT, '#', [
					'data-yjs-request' => 'crud/load',
					'data-yjs-target' => 'modal',
				    'data-yjs-datasource-url' => Router::url($viewUrl),
				    'data-yjs-event-on' => 'click',
				    'data-yjs-modal-size-width' => 80,
					'escape' => false
				])
			]);
		}
    }

    public function attachment(OutputBuilder $output, $subject)
    {
    	$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

        foreach ($FieldsIterator as $key => $value) {
        	if ($value['item']->use_attachments == SecurityPolicy::CONTENT_TYPE_ATTACHMENTS) {
				$html = $subject->view->Html->link(__('Attachments'), '#', [
					'data-yjs-request' => 'crud/showForm',
					'data-yjs-target' => 'modal',
				    'data-yjs-datasource-url' => Router::url([
				    	'plugin' => 'widget',
				    	'controller' => 'widget',
						'action' => 'index',
						'SecurityPolicy',
						$value['item']->getPrimary(),
						'?' => [
							'tabActive' => 1 // attachments tab is 1
						]
					]),
				    'data-yjs-event-on' => "click",
					'escape' => false
				]);

				$output->label([
					$key => $html
				]);
			}
		}
    }
}