<?php
App::uses('AppHelper', 'View/Helper');
App::uses('CakeText', 'Utility');
App::uses('CakeNumber', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('Attachment', 'Attachments.Model');

class AttachmentsHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Html', 'Ux', 'LimitlessTheme.Icons', 'LimitlessTheme.Popovers', 'FieldData.FieldData', 'AclCheck'];

	public static $icons = [
		'image/bmp' => 'file-picture',
		'image/gif' => 'file-picture',
		'image/ief' => 'file-picture',
		'image/jpeg' => 'file-picture',
		'image/jpeg' => 'file-picture',
		'image/png' => 'file-picture',
		'image/png' => 'file-picture',
		'image/tiff' => 'file-picture',
		'image/vnd.adobe.photoshop' => 'file-picture',
		'image/vnd.dwg' => 'file-picture',
		'image/x-icon' => 'file-picture',
		'image/x-pcx' => 'file-picture',
		'image/x-pict' => 'file-picture',

		'text/csv' => 'file-text2',
		
		'video/mp4' => 'file-video',
		'video/mpeg' => 'file-video',
		'video/ogg' => 'file-video',
		'video/webm' => 'file-video',
		'video/x-f4v' => 'file-video',
		'video/x-msvideo' => 'file-video',

		'application/msword' => 'file-text2',
		'application/vnd.ms-excel' => 'file-spreadsheet',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'file-text2',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'file-text2',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'file-text2',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'file-text2',
		'application/vnd.ms-powerpoint' => 'file-presentation',
		'application/vnd.ms-project' => 'file-text2',
		'application/pdf' => 'file-text2',

		'application/vnd.oasis.opendocument.text' => 'file-text2',
		'application/vnd.oasis.opendocument.text-template' => 'file-text2',
		'application/vnd.oasis.opendocument.text-web' => 'file-text2',
		'application/vnd.oasis.opendocument.text-master' => 'file-text2',
		'application/vnd.oasis.opendocument.graphics' => 'file-text2',
		'application/vnd.oasis.opendocument.graphics-template' => 'file-text2',
		'application/vnd.oasis.opendocument.presentation' => 'file-text2',
		'application/vnd.oasis.opendocument.presentation-template' => 'file-text2',
		'application/vnd.oasis.opendocument.spreadsheet' => 'file-text2',
		'application/vnd.oasis.opendocument.spreadsheet-template' => 'file-text2',
		'application/vnd.oasis.opendocument.chart' => 'file-text2',
		'application/vnd.oasis.opendocument.formula' => 'file-text2',
		'application/vnd.oasis.opendocument.database' => 'file-text2',
		'application/vnd.oasis.opendocument.image' => 'file-text2',
		'application/vnd.openofficeorg.extension' => 'file-text2',

		'application/zip' => 'file-zip',
		'application/gzip' => 'file-zip',
		'application/octet-stream' => 'file-zip',
		'text/plain' => 'file-text2',

		'application/vnd.visio' => 'file-picture',
		'application/vnd.visio2013' => 'file-picture',
		'application/octet-stream' => 'file-picture',
	];

    public function renderStoryItem($data)
    {
        $avatar = $this->Html->div('media-left', $this->Ux->createLetterUserPic($data['User']['full_name']));

        $timeText = CakeTime::timeAgoInWords($data['Attachment']['created'], [
            'end' => '1 day',
            'format' => 'Y-m-d'
        ]);
        $time = $this->Html->tag('span', $timeText, [
            'class' => 'media-annotation dotted'
        ]);

        $header = $this->Html->div('media-heading text-primary', $data['User']['full_name'] . ' ' . $time);
        
        $attachment = $this->Html->div('attachments-list', $this->renderItem($data));

        $body = $this->Html->div('media-body', $header . $attachment);

        return $this->Html->tag('li', $avatar . $body, [
            'class' => 'media'
        ]);
    }

	public function renderItem($data)
	{
		$size = $this->Html->div('dz-size', $this->_size($data) . $this->_actions($data));
		$name = $this->Html->div('dz-filename', $this->_filename($data));

		$details = $this->Html->div('dz-details', $size . $name);
		$img = $this->Html->div('dz-image', $this->_image($data));

		return $this->Html->div('dz-preview dz-file-preview', $img . $details);
	}

	protected function _filename($data)
	{
		$filename = basename($data['Attachment']['name']);

		return $this->Popovers->top($filename, $filename);
	}

	protected function _size($data)
	{
		return CakeNumber::toReadableSize($data['Attachment']['file_size']);
	}

	protected function _actions($data)
	{
		$actions = [];

        $downloadUrl = $this->downloadUrl($data);

		if (!empty($data['Attachment']['model']) && $this->AclCheck->check($downloadUrl)) {
			$actions[] = $this->Html->link($this->Icons->render('arrow-down16'), $downloadUrl, [
				'escape' => false
			]);
		}

        $deleteUrl = $this->deleteUrl($data);

        if ($this->AclCheck->check($deleteUrl)) {
            $actions[] = $this->Html->link($this->Icons->render('bin'), '#', [
                'escape' => false,
                'data-yjs-request' => 'crud/showForm',
                'data-yjs-target' => 'modal',
                'data-yjs-datasource-url' => $deleteUrl,
                'data-yjs-event-on' => 'click',
            ]);
        }

		return $this->Html->div('dz-actions', implode(' ', $actions));
	}

	protected function _image($data)
	{
		$icon = 'file-empty';
		if (!empty(self::$icons[$data['Attachment']['mime_type']])) {
			$icon = self::$icons[$data['Attachment']['mime_type']];
		}

		return $this->Icons->render($icon);
	}

	public function renderList($data)
	{
		$list = [];

		foreach ($data as $item) {
			$list[] = $this->renderItem($item);
		}

		return implode('', $list);
	}

	public function downloadUrl($data)
	{
		$Model = ClassRegistry::init($data['Attachment']['model']);

        return Router::url([
        	'plugin' => Inflector::underscore($Model->plugin),
            'controller' => $Model->getMappedController(),
            'action' => 'downloadAttachment',
            $data['Attachment']['id']
        ]);
    }

    public function deleteUrl($data)
	{
        return Router::url([
        	'plugin' => 'attachments',
            'controller' => 'attachments',
            'action' => 'delete',
            $data['Attachment']['id']
        ]);
    }

    public function attachmentTmpField(FieldDataEntity $Field)
    {
    	$hash = $this->_View->get('attachmentHash');

    	$label = $this->Html->tag('label', $Field->getLabel());
    	$desc = (empty($Field->getDescription())) ? '' : $this->Html->tag('span', $Field->getDescription(), [
    		'class' => 'help-block'
		]);

		$error = $this->FieldData->error($Field);

		$content = $label . $desc . $error . $this->Html->div('attachments form-group', '', [
			'id' => 'attachments-list',
			'data-yjs-request' => 'crud/load',
			'data-yjs-target' => '#attachments-list',
			'data-yjs-datasource-url' => Router::url([
				'plugin' => 'attachments',
				'controller' => 'attachments',
				'action' => 'indexTmp',
				$hash
			]),
			'data-yjs-event-on' => 'init',
			'data-yjs-use-loader' => 'false',
		]);

		return $content;
    }

    public function attachmentField(FieldDataEntity $Field)
    {
    	$hash = $this->_View->get('attachmentHash');
    	$modelName = $this->_View->get('attachmentModel');
    	$foreignKey = $this->_View->get('attachmentForeignKey');

    	$content = '';

    	if ($hash !== null) {
    		$content = $this->attachmentTmpField($Field);
    	}
    	elseif ($modelName !== null && $foreignKey !== null) {
    		$label = $this->Html->tag('label', $Field->getLabel());
	    	$desc = (empty($Field->getDescription())) ? '' : $this->Html->tag('span', $Field->getDescription(), [
	    		'class' => 'help-block'
			]);

			$error = $this->FieldData->error($Field);

    		$content = $label . $desc . $error . $this->Html->div('', '', [
				'id' => 'attachments-list',
				'class' => ['attachments'],
				'data-yjs-request' => 'crud/load',
				'data-yjs-target' => '#attachments-list',
				'data-yjs-datasource-url' => Router::url([
					'plugin' => 'attachments',
					'controller' => 'attachments',
					'action' => 'index',
					$modelName,
					$foreignKey
				]),
				'data-yjs-event-on' => 'init',
				'data-yjs-use-loader' => 'false',
			]);
    	}

		return $content;	
    }
}
