<?php
class AdvancedFilterCron extends AppModel {

    const TYPE_COUNT = 1;
    const TYPE_DATA = 2;

    const EXPORT_ROWS_LIMIT = 10000;

    public $belongsTo = array(
        'Cron'
    );

    public $virtualFields = array(
        'date' => 'DATE(AdvancedFilterCron.created)',
    );

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Advanced Filter Crons');
        
        parent::__construct($id, $table, $ds);
    }

    /**
     * Save info record about a cron task that was run.
     */
    public function saveCronTaskRecord($data) {
        $this->create();
        $this->set($data);
        return $this->save(null, false);
    }
}
