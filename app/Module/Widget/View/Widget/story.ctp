<?php
App::uses('Hash', 'Utility');

$data = array_merge($comments, $attachments);

foreach ($data as $key => $item) {
    $modelName = isset($item['Comment']) ? 'Comment' : 'Attachment';
    $data[$key]['created'] = $item[$modelName]['created'];
}

$data = Hash::sort($data, '{n}.created', 'desc');

if (empty($data)) {
    echo $this->Alerts->info(__('No comments and attachments for this record.'));
    return;
}

$list = [];

foreach ($data as $item) {
    if (isset($item['Comment'])) {
        $list[] = $this->Comments->renderItem($item);
    }
    else {
        $list[] = $this->Attachments->renderStoryItem($item);
    }
}

echo $this->Html->tag('ul', implode('', $list), [
    'class' => ['comments-list',  'media-list', 'stack-media-on-mobile', 'text-left']
]);
