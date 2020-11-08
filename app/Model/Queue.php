<?php
App::uses('AppModel', 'Model');
App::uses('File', 'Utility');
App::uses('BulkAction', 'BulkActions.Model');
App::uses('SystemHealthLib', 'Lib');

class Queue extends AppModel
{
    public $name = 'Queue';
    public $useTable = 'queue';

    public $mapController = 'queue';

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

    public $actsAs = array(
        'Search.Searchable',
        'HtmlPurifier.HtmlPurifier' => array(
            'config' => 'Strict',
            'fields' => array()
        ),
        'Comments.Comments',
        'Attachments.Attachments',
        'Widget.Widget',
        'AdvancedFilters.AdvancedFilters'
    );

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_CREATED => __('Created'),
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_SUCCESS => __('Success'),
            self::STATUS_FAILED => __('Failed'),
            self::STATUS_FILE_NOT_EXISTS => __('File not exists')
        );
        return parent::enum($value, $options);
    }

    const STATUS_CREATED = 3;
    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
    const STATUS_FILE_NOT_EXISTS = 4;

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Queue');

        $this->fieldGroupData = array(
            'default' => array(
                'label' => __('General')
            ),
        );

        $this->fieldData = array(
            'queue_id' => array(
                'label' => __('Queue ID'),
                'editable' => false
            ),
            'model' => array(
                'label' => __('Model'),
                'editable' => false
            ),
            'foreign_key' => array(
                'label' => __('Record ID'),
                'editable' => false
            ),
            'description' => array(
                'label' => __('Description'),
                'editable' => false
            ),
            'status' => array(
                'label' => __('Status'),
                'options' => array($this, 'getStatuses'),
                'editable' => false
            ),
        );

        $this->advancedFilterSettings = array(
            'pdf_title' => __('Queue'),
            'pdf_file_name' => __('queue'),
            'csv_file_name' => __('queue'),
            'max_selection_size' => 10,
            'actions' => false,
            'url' => array(
                'controller' => 'queue',
                'action' => 'index',
                '?' => array(
                    'advanced_filter' => 1
                )
            ),
            'reset' => array(
                'controller' => 'queue',
                'action' => 'index',
                '?' => array(
                    'advanced_filter' => 1
                )
            ),
            'bulk_actions' => array(
                BulkAction::TYPE_DELETE,
            ),
            'include_timestamps' => false,
            'use_new_filter' => true
        );

        parent::__construct($id, $table, $ds);
    }

    public function getAdvancedFilterConfig()
    {
        $advancedFilterConfig = $this->createAdvancedFilterConfig()
            ->group('general', [
                'name' => __('General')
            ])
                ->nonFilterableField('id')
                ->textField('queue_id', [
                    'showDefault' => true
                ])
                ->textField('model', [
                    'showDefault' => true
                ])
                ->textField('foreign_key', [
                    'showDefault' => true
                ])
                ->textField('description', [
                    'showDefault' => true
                ])
                ->selectField('status', [$this, 'statuses'], [
                    'showDefault' => true
                ])
                ->dateField('created', [
                    'label' => __('Date'),
                    'showDefault' => true
                ]);

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

    public function beforeDelete($cascade = true) {
        $queueItem = $this->find('first', array(
            'conditions' => array(
                'Queue.id' => $this->id
            ),
            'recursive' => -1
        ));

        if (!empty($queueItem)) {
            $this->_deleteItemData($queueItem['Queue']['queue_id'], $queueItem['Queue']['id']);
        }
        
        return true;
    }

    public function getStatuses() {
        return self::statuses();
    }

    /**
     * Insert email to queue.
     * 
     * @param ErambaCakeEmail $data
     * @return mixed false on fail | Queue.id on success
     */
    public function add($data) {
        // $queueId = $data->getQueueId();

        // $item = $this->find('count', array(
        //     'conditions' => array(
        //         'queue_id' => $queueId,
        //         'data' => $serializedData,
        //     )
        // ));

        // if (!empty($item)) {
        //     $trace = Debugger::trace(array('start' => 1, 'format' => 'log'));
        //     CakeLog::write('error', 'Duplicit email in queue with queue_id ' . $data->getQueueId() . "\n" . 'Stack Trace:' .  "\n" . $trace);

        //     return true;
        // }

        $to = (!is_array($data->to())) ? explode(',', $data->to()) : $data->to();
        $to = array_merge($to, (!is_array($data->cc())) ? explode(',', $data->cc()) : $data->cc());
        $to = array_merge($to, (!is_array($data->bcc())) ? explode(',', $data->bcc()) : $data->bcc());
        $to = array_unique($to);

        $recipients = implode(', ', $to);

        $this->create(array(
            'queue_id' => $data->queueId(),
            'model' => $data->model(),
            'foreign_key' => $data->foreignKey(),
            'status' => self::STATUS_CREATED,
            'description' => __('Email to %s', $recipients)
        ));
        $result = $this->save();

        if ($result) {
            $result &= $this->_writeItemData($data, $this->id);
            $result &= $this->markAsPending();
        }

        return ($result) ? $this->id : false;
    }

    /**
     * returns all pending queue items
     *
     * @param int $limit
     * @param array $conditions Additional conditions on id, queue_id.
     * @return array
     */
    public function getPending($limit, $conditions = []) {
        $defaultConditions = [
            'Queue.status' => self::STATUS_PENDING
        ];

        $conditions = array_merge($defaultConditions, $conditions);

        return $this->find('all', [
            'conditions' => $conditions,
            'limit' => $limit,
            'order' => array('Queue.created' => 'ASC')
        ]);
    }

    /**
     * changes status of queue item to success
     * 
     * @param  array $item queue item
     * @return mixed false on fail | array on success
     */
    public function markAsSuccess($item) {
        $this->create($item);
        $result = $this->save([
            'status' => self::STATUS_SUCCESS,
            'data' => null // we purge the serialized class that is not needed anymore
        ]);

        if ($result) {
            $this->_deleteItemData($item['Queue']['queue_id'], $item['Queue']['id']);
        }

        return $result;
    }

    /**
     * changes status of queue item to failed
     * 
     * @param  array $item queue item
     * @return mixed false on fail | array on success
     */
    public function markAsFailed($item) {
        $this->create($item);
        return $this->save([
            'status' => self::STATUS_FAILED
        ]);
    }

    /**
     * changes status of queue item to pending
     * 
     * @param  array $item queue item
     * @return mixed false on fail | array on success
     */
    public function markAsPending($item = null) {
        if ($item !== null) {
            $this->create($item);
        }
        
        return $this->save([
            'status' => self::STATUS_PENDING
        ]);
    }

    /**
     * changes status of queue item to FILE_NOT_EXISTS
     * 
     * @param  array $item queue item
     * @return mixed false on fail | array on success
     */
    public function markAsFileNotExists($item)
    {
        $this->create($item);

        return $this->save([
            'status' => self::STATUS_FILE_NOT_EXISTS
        ]);
    }

    /**
     * writes emails serialized data to vendors folder
     * 
     * @param  array $data emails data
     * @param  int $queueItemId
     * @return boolean
     */
    protected function _writeItemData($data, $queueItemId) {
        $serializedData = serialize($data);
        $fileName = self::getFileName($data->queueId(), $queueItemId);

        $file = new File(self::getDataPath() . $fileName, true);
        if (!$file->writable()) {
            return false;
        }

        $result = $file->write($serializedData);
        $file->close();

        return $result;
    }

    /**
     * deletes item data from vendor folder
     * 
     * @param  string|int $queueId 
     * @param  string|int $queueItemId 
     * @return boolean
     */
    protected function _deleteItemData($queueId, $queueItemId) {
        $fileName = self::getFileName($queueId, $queueItemId);

        $file = new File(self::getDataPath() . $fileName);

        if (!$file->exists()) {
            return true;
        }

        return $file->delete();
    }

    /**
     * returns unserialized data for input queueId and queueItemId
     * 
     * @param  string|int $queueId 
     * @param  string|int $queueItemId 
     * @return ErambaCakeEmail data | false on failure
     */
    public static function getItemData($queueId, $queueItemId) {
        $fileName = self::getFileName($queueId, $queueItemId);

        $file = new File(self::getDataPath() . $fileName);

        if (!$file->exists()) {
            return false;
        }

        $content = $file->read();

        $data = unserialize($content);

        return $data;
    }

    /**
     * queue email data path
     * 
     * @return String
     */
    public static function getDataPath() {
        return SystemHealthLib::getDataPath();
    }

    /**
     * returns item file name for input queueId and queueItemId
     *
     * @param  string|int $queueId 
     * @param  string|int $queueItemId 
     * @return String
     */
    public static function getFileName($queueId, $queueItemId) {
        return sprintf('%s_%s', $queueId, $queueItemId) . '.txt';
    }

    public function syncIndex($listUsers = null)
    {
        $AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
        if ($listUsers === null) {
            $listUsers = $AdvancedFilter->getUsersToSync();
        }

        $AdvancedFilterValue = $AdvancedFilter->buildShowDefaultFields('Queue');
        $AdvancedFilterValue[] = [
            'field' => 'status',
            'value' => 1
        ];

        $AdvancedFilterValue2 = $AdvancedFilter->buildShowDefaultFields('Queue');
        $AdvancedFilterValue2[] = [
            'field' => 'status',
            'value' => 0
        ];

        $ret = true;
        foreach ($listUsers as $userId) {
            $AdvancedFilter->create();
            $ret &= $AdvancedFilter->saveAssociated([
                'AdvancedFilter' => [
                    'user_id' => $userId,
                    'name' => __('Sent Emails'),
                    'slug' => 'sent-emails',
                    'description' => __('Filter shows sent emails'),
                    'model' => 'Queue',
                    'private' => 1,
                    'log_result_data' => 0,
                    'log_result_count' => 0,
                    'system_filter' => 1
                ],
                'AdvancedFilterUserSetting' => [
                    'model' => 'Queue',
                    'default_index' => '1',
                    'user_id' => $userId
                ],
                'AdvancedFilterValue' => $AdvancedFilterValue
            ]);

            $AdvancedFilter->create();
            $ret &= $AdvancedFilter->saveAssociated([
                'AdvancedFilter' => [
                    'user_id' => $userId,
                    'name' => __('Pending Emails'),
                    'slug' => 'pending-emails',
                    'description' => __('Filter show emails still pending to be sent'),
                    'model' => 'Queue',
                    'private' => 1,
                    'log_result_data' => 0,
                    'log_result_count' => 0,
                    'system_filter' => 1
                ],
                'AdvancedFilterUserSetting' => [
                    'model' => 'Queue',
                    'default_index' => '1',
                    'user_id' => $userId
                ],
                'AdvancedFilterValue' => $AdvancedFilterValue2
            ]);
        }

        return $ret;
    }
}

