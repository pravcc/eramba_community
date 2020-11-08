<?php
App::uses('CakeObject', 'Core');
App::uses('AdvancedFilterCron', 'Model');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');

class AdvancedFiltersCronLib extends CakeObject
{
	private $error = false;
    private $errorMessages = [];
    private $crons = [];

    public function getErrorMessages($implode = false) {
        return ($implode) ? implode(' ', $this->errorMessages) : $this->errorMessages;
    }

    private function error($message) {
        $this->error = true;
        $this->errorMessages[] = $message;
    }

	/**
     * execute all advanced filters with enabled log
     * 
     * @return boolean - success/failed
     */
    public function execute() {
        $filters = ClassRegistry::init('AdvancedFilters.AdvancedFilter')->find('all', array(
            'conditions' => array(
                'OR' => array(
                    'AdvancedFilter.log_result_count' => ADVANCED_FILTER_LOG_ACTIVE,
                    'AdvancedFilter.log_result_data' => ADVANCED_FILTER_LOG_ACTIVE,
                )
            ),
            'contain' => array(
                // 'AdvancedFilterValue',
                'AdvancedFilterCron' => array(
                    'conditions' => array(
                        'DATE(AdvancedFilterCron.created)' => date('Y-m-d')
                    )
                )
            )
        ));

        $ret = true;
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                if (!empty($filter['AdvancedFilterCron'])) {
                    continue;
                }
                if ($filter['AdvancedFilter']['log_result_count'] == ADVANCED_FILTER_LOG_ACTIVE) {
                    $ret &= $this->runFilter($filter, AdvancedFilterCron::TYPE_COUNT);
                }
                if ($filter['AdvancedFilter']['log_result_data'] == ADVANCED_FILTER_LOG_ACTIVE) {
                    $ret &= $this->runFilter($filter, AdvancedFilterCron::TYPE_DATA);
                    //remove obsolete items
                    $ret &= ClassRegistry::init('AdvancedFilterCronResultItem')->removeObsoleteItems($filter['AdvancedFilter']['id']);
                }
            }
        }

        return $ret;
    }

    /**
     * execute advanced filter, create cron record
     * 
     * @param  array $filter
     * @param  $type
     * @return boolean - success/failed
     */
    private function runFilter($filter, $type) {
        $mapTypeToFindOperation = [
            AdvancedFilterCron::TYPE_COUNT => 'count',
            AdvancedFilterCron::TYPE_DATA => 'all'
        ];

        $startTime = scriptExecutionTime();

        $filterId = $filter['AdvancedFilter']['id'];
        $model = $filter['AdvancedFilter']['model'];

        // $this->AdvancedFilters->buildRequest($filter['AdvancedFilter']['id']);

        // $this->initAdvancedFilter($model);

        App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');

        $_filter = new AdvancedFiltersObject($filterId);
        $result = $_filter->filter($mapTypeToFindOperation[$type]);

        // $result = $this->AdvancedFilters->filterCron($filter, $type);
        // $this->controller->set('data', $result);

        $time = scriptExecutionTime() - $startTime;
        $ret = $this->processFilterResult($filter, $result, $time, $type);

        // $this->controller->request->query = array();
        // $this->controller->request->data = array();
        
        return ($result !== false && $ret) ? true : false;
    }

    /**
     * proccess filter result, save advanced filter cron record
     *
     * @param  array $filter
     * @param  mixed $filterResult - filter result
     * @param  float $time - execution time
     * @param  int $type - filter type (count/data)
     * @return boolean - success/failed
     */
    private function processFilterResult($filter, $filterResult, $time, $type) {
        if ($filterResult === false) {
            $this->error(__('Advanced Filter Cron failed (advanced_filter_id = %s)', $filter['AdvancedFilter']['id']));
        }

        $data = array(
            'advanced_filter_id' => $filter['AdvancedFilter']['id'],
            'type' => $type,
            'result' => $this->getResultFromData($filterResult, $type),
            'execution_time' => $time,
        );

        $saveLog = $this->crons[] = ClassRegistry::init('AdvancedFilterCron')->saveCronTaskRecord($data);
        if (!empty($saveLog['AdvancedFilterCron']['id']) && $type == AdvancedFilterCron::TYPE_DATA && !empty($filterResult)) {
            $filterCronId = $saveLog['AdvancedFilterCron']['id'];
            foreach ($filterResult as $item) {
                $saveLog &= ClassRegistry::init('AdvancedFilterCronResultItem')->saveResultItem($filterCronId, $item);
            }
        }
        if (!$saveLog) {
            $this->error(__('Advanced Filter Cron - record saving failed (advanced_filter_id = %s)', $filter['AdvancedFilter']['id']));
        }

        return $saveLog;
    }

    /**
     * returns data to ready DB record
     * 
     * @param  mixed $filterResult - filter result
     * @param  int $type - filter type (count/data)
     * @return mixed $result
     */
    private function getResultFromData($filterResult, $type) {
        $result = 0;
        if (is_numeric($filterResult)) {
            $result = $filterResult;
        }
        elseif (is_array($filterResult)) {
            $result = count($filterResult);
        }
        elseif ($filterResult instanceof ItemDataCollection) {
            $result = $filterResult->count();
        }

        return $result;
    }

    /**
     * save cron_id to created advanced filter crons records
     * 
     * @param  int $cronId
     * @return  boolean success/failed
     */
    public function assignCronIdToRecords($cronId) {
        if (empty($this->crons)) {
            return true;
        }

        $filterCronIds = array();
                
        foreach ($this->crons as $item) {
            $filterCronIds[] = $item['AdvancedFilterCron']['id'];
        }

        return ClassRegistry::init('AdvancedFilterCron')->updateAll(array('cron_id' => $cronId), array(
            'AdvancedFilterCron.id' => $filterCronIds
        ));
    }
}