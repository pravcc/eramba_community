<?php
App::uses('AppHelper', 'View/Helper');
App::uses('CakeText', 'Utility');

class CommentsHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Form', 'Html', 'Ux', 'Content', 'Paginator', 'LimitlessTheme.Icons', 'LimitlessTheme.Buttons', 'AclCheck'];

	public function renderStoryItem()
	{
		return $this->renderItem($data);
	}

	public function renderItem($data) {
		$logged = $Trash = $this->_View->get('logged');

		$avatar = $this->Html->div('media-left', $this->Ux->createLetterUserPic($data['User']['full_name']));

		$timeText = CakeTime::timeAgoInWords($data['Comment']['created'], [
			'end' => '1 day',
			'format' => 'Y-m-d'
		]);
		$time = $this->Html->tag('span', $timeText, [
			'class' => 'media-annotation dotted'
		]);

		$delete = '';
		$deleteUrl = $this->deleteUrl($data);

		if (!empty($logged['id']) && $logged['id'] == $data['Comment']['user_id'] && $this->AclCheck->check($deleteUrl)) {
			$deleteLink = $this->Html->link('Delete', '#', [
				'escape' => false,
				'data-yjs-request' => 'crud/showForm',
				'data-yjs-target' => 'modal',
				'data-yjs-datasource-url' => $deleteUrl,
				'data-yjs-event-on' => 'click',
			]);
			$delete = $this->Html->tag('span', $deleteLink, [
				'class' => 'media-annotation dotted'
			]);
		}
		

		$header = $this->Html->div('media-heading text-primary', $data['User']['full_name'] . ' ' . $time . ' ' . $delete);
		$message = $this->Html->tag('p', $this->Content->text($data['Comment']['message']));

		$body = $this->Html->div('media-body', $header . $message);

		return $this->Html->tag('li', $avatar . $body, [
			'class' => 'media'
		]);
	}

	public function renderList($data) {
		$list = [];

		foreach ($data as $item) {
			$list[] = $this->renderItem($item);
		}

		return $this->_getList(implode('', $list));
	}

	public function renderNextLink() {
		if (!$this->Paginator->hasNext()) {
			return '';
		}

		$containerId = 'comments-list-' . CakeText::uuid();

		$button = $this->Buttons->default(__('Load More'), [
			'data' => [
				'yjs-request' => 'crud/load',
				'yjs-target' => "#$containerId",
				'yjs-datasource-url' => Router::url(array_merge($this->_View->request->params['pass'], [
					'page' => ($this->Paginator->current() + 1)
				])),
				'yjs-event-on' => 'click',
			]
		]);

		return $this->Html->div('comments-list-page text-center', '<br>' . $button, [
			'id' => $containerId
		]);
	}

	protected function _getList($content) {
		return $this->Html->tag('ul', $content, [
			'class' => ['comments-list',  'media-list', 'stack-media-on-mobile', 'text-left']
		]);
	}

	public function deleteUrl($data)
	{
        return Router::url([
        	'plugin' => 'comments',
            'controller' => 'comments',
            'action' => 'delete',
            $data['Comment']['id']
        ]);
    }

	public function placeholder($count = 1) {
		$placeholder = '<li class="media">
			<div class="media-left">
				<span class="default-user-pic light-gray pulse-animation">
				</span>
			</div>
			<div class="media-body">
				<div class="media-heading text-primary">
					<span class="content-placeholer" style="width: 30%;"></span>
				</div>
				<p>
					<span class="content-placeholer"></span>
				</p>
			</div>
		</li>';

		$min = 1;
		$max = 8;

		$count = max(min($max, $count), $min);

		$content = '';

		for ($i = 1; $i <= $count; $i++) {
			$content .= $placeholder;
		}

		return $this->_getList($content);
	}
}
