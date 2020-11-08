<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('SecurityPolicy', 'Model');

class SecurityPolicyReviewRenderProcessor extends SectionRenderProcessor
{
	public function useAttachments(OutputBuilder $output, $subject)
	{
		$item = $subject->item;
		$view = $subject->view;
		$itemKey = $output->getKey($subject->item, $subject->field);

		$html = null;
		$assumeTypeConds = $item->use_attachments == null;
		$assumeTypeConds &= $item->completed == SecurityPolicyReview::STATUS_COMPLETE;
		// AttachmentsProperty method
		$assumeTypeConds &= $item->attachmentsCount() > 0;

		// in case document type is missing from the records, assume if its attachments
		// by checking existing attachments to the review
		if ($assumeTypeConds) {
			$item->use_attachments = SecurityPolicy::CONTENT_TYPE_ATTACHMENTS;
		}

		if ($item->use_attachments != null) {
			$html = getPoliciesDocumentContentTypesWithoutUse($item->use_attachments);

			// for url type
			if ($item->use_attachments == SecurityPolicy::CONTENT_TYPE_URL) {
				$url = $item->url;
				$html = $view->Html->link($html, $url, [
					'target' => '_blank'
				]);
			}

			// for editor content type
			if ($item->use_attachments == SecurityPolicy::CONTENT_TYPE_EDITOR) {
				// $url = $item->url;
				// $html = $view->Html->link($html, $url, [
				// 	'target' => '_blank'
				// ]);
				

				$html = $view->Html->link($html, '#', [
					'data-yjs-request' => 'crud/load',
					'data-yjs-target' => 'modal',
					'data-yjs-datasource-url' => Router::url([
				    	'controller' => 'securityPolicyReviews',
						'action' => 'review',
						$item->getPrimary()
					]),
				    'data-yjs-event-on' => "click",
				    'data-yjs-modal-size-width' => 80,
					'escape' => false
				]);
			}

			// for attachments type
			if ($item->use_attachments == SecurityPolicy::CONTENT_TYPE_ATTACHMENTS) {
				$html = $view->Html->link($html, '#', [
					'data-yjs-request' => 'crud/showForm',
					'data-yjs-target' => 'modal',
				    'data-yjs-datasource-url' => Router::url([
				    	'plugin' => 'widget',
				    	'controller' => 'widget',
						'action' => 'index',
						'SecurityPolicyReview',
						$item->getPrimary(),
						'?' => [
							'tabActive' => 1 // attachments tab is 1
						]
					]),
				    'data-yjs-event-on' => "click",
					'escape' => false
				]);
			}
		} else {
			$html = '-';
		}

		$output->label([$itemKey => $html]);
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
						'SecurityPolicyReview',
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