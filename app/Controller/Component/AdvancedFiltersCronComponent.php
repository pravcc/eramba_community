<?php
App::uses('Component', 'Controller');
App::uses('AdvancedFiltersData', 'Lib');
App::uses('AdvancedFilterCron', 'Model');
App::uses('Cron', 'Model');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');

class AdvancedFiltersCronComponent extends Component {

    // const STATUS_SUCCESS = 'success';
    // const STATUS_ERROR = 'error';

    // const TYPE_COUNT = ADVANCED_FILTER_CRON_COUNT;
    // const TYPE_DATA = ADVANCED_FILTER_CRON_DATA;

    // const EXPORT_ROWS_LIMIT = 5000;

    protected $_defaults = array();

    public $components = ['AdvancedFilters', 'Crud.Crud'];

    public $settings = [
        'listenerClass' => 'AdvancedFilters.AdvancedFiltersCron'
    ];

    public function __construct(ComponentCollection $collection, $settings = []) {
        if (empty($this->settings)) {
            $this->settings = [];
        }

        $settings = array_merge($this->settings, (array)$settings);
        parent::__construct($collection, $settings);
    }

    public function initialize(Controller $controller) {
        $this->controller = $controller;

        // $this->Crud->addListener('AdvancedFilterCron', $this->settings['listenerClass']);

        $this->controller->loadModel('AdvancedFilters.AdvancedFilter');
        $this->controller->loadModel('AdvancedFilterCron');
        $this->controller->loadModel('AdvancedFilterCronResultItem');
    }

    public function exportDataResults($fiterId, $type = 'csv') {
        $fiterId = (int) $fiterId;

        $filter = $this->controller->AdvancedFilter->getFilter($fiterId);

        if (empty($filter)) {
            return false;
        }

        $model = $filter['AdvancedFilter']['model'];

        $this->AdvancedFilters->buildRequest($fiterId);
        $this->initAdvancedFilter($model);

        if ($type == 'csv') {
            $this->AdvancedFilters->csv($model);
        }
        else {
            $this->AdvancedFilters->pdf($model);
        }

        $this->controller->request->query = array();
        $this->controller->set('cronFilterRequestData', $this->controller->request->data);
        $this->controller->request->data = array();

        $fileName = Inflector::slug($filter['AdvancedFilter']['name'], '-') . '_daily-data-results';

        return $fileName;
    }

    /**
     * sets data for count results export
     * 
     * @param  int $fiterId
     * @return mixed - (string) filename/(bool) false
     */
    public function exportDailyCountResults($fiterId) {
        $fiterId = (int) $fiterId;

        $failedJobs = $this->controller->AdvancedFilter->AdvancedFilterCron->Cron->getFailedJobIds();
        $filter = $this->controller->AdvancedFilter->find('first', array(
            'conditions' => array(
                'AdvancedFilter.id' => $fiterId
            ),
            'contain' => array(
                'AdvancedFilterCron' => array(
                    'conditions' => array(
                        'AdvancedFilterCron.type' => AdvancedFilterCron::TYPE_COUNT,
                        'AdvancedFilterCron.cron_id !=' => $failedJobs 
                    ),
                    'limit' => AdvancedFilterCron::EXPORT_ROWS_LIMIT,
                    'order' => array('AdvancedFilterCron.created' => 'DESC'),
                )
            )
        ));

        if (empty($filter)) {
            return false;
        }

        $this->setCountCsvData($filter);

        $fileName = Inflector::slug($filter['AdvancedFilter']['name'], '-') . '_daily-count-results';

        return $fileName;
    }

    /**
     * sets data for data results export
     * 
     * @param  int $fiterId
     * @param  string $type csv|pdf
     * @return mixed - (string) filename/(bool) false
     */
    public function exportDailyDataResults($fiterId) {
        $fiterId = (int) $fiterId;

        $filter = $this->controller->AdvancedFilter->find('first', array(
            'conditions' => array(
                'AdvancedFilter.id' => $fiterId,
            ),
            'contain' => array('AdvancedFilterValue')
        ));

        if (empty($filter)) {
            return false;
        }

        $filterData = $this->getDataCronData($fiterId);

        $this->setDataCsvData($filterData, $filter);

        $fileName = Inflector::slug($filter['AdvancedFilter']['name'], '-') . '_daily-data-results';

        return $fileName;
    }

    /**
     * init advanced filter
     * 
     * @param  string $model 
     */
    private function initAdvancedFilter($model) {
        $this->controller->loadModel($model);
        
        $this->controller->presetVars = null;

        $this->controller->Components->unload('AdvancedFilters');
        $this->controller->Components->unload('Search.Prg');

        Configure::write('Search.Prg.presetForm', array('model' => $model));

        $this->AdvancedFilters = $this->controller->Components->load('AdvancedFilters');
        $this->AdvancedFilters->initialize($this->controller);
        $this->AdvancedFilters->resetCustomFields($model);
    }

    /**
     * sets data for daily count CSV
     * 
     * @param array $filter
     */
    private function setCountCsvData($filter) {
        $_header = array('Date', 'Results Count');
        $_extract = array('date', 'count');
        $data = array();
        $_serialize = 'data';

        foreach ($filter['AdvancedFilterCron'] as $item) {
            $data[] = array('date' => $item['date'], 'count' => $item['result']);
        }

        $this->controller->set(compact('_header', '_extract', 'data', '_serialize'));
    }

     private function setDataCsvData($filtersData, $filter) {
        App::uses('View', 'View');

        $model = $filter['AdvancedFilter']['model'];
        $filterId = $filter['AdvancedFilter']['id'];

        $_filter = new AdvancedFiltersObject($filterId);
        $_filter->filter();
        $showableFields = $_filter->getShowableFields();

        
        $CollectionsArr = [];
        foreach ($filtersData as $item) {
            if (empty($item['data'])) {
                continue;
            }

            $Collection = ClassRegistry::init($model)->getItemDataCollection();
            foreach ($item['data'] as $dataItem) {
                $Collection->add($dataItem);
            }

            $CollectionsArr[] = $Collection;
        }

        $_header = ['Date'];
        $_extract = ['__cron_date'];
        foreach ($showableFields as $FilterField) {
            $_header[] = $FilterField->getLabel();
            $_extract[] = $FilterField->getFieldName();
        }

        $View = new View();
        $View->loadHelper('ObjectRenderer.ObjectRenderer');
        
        $data = [];
        $key = 0;
        foreach ($CollectionsArr as $Collection) {
            foreach ($Collection as $Item) {
                $data[$key]['__cron_date'] = $Item->__cron_date;
                foreach ($showableFields as $field => $FilterField) {
                    $TraverserData = traverser($Item, $FilterField);

                    $content = '';
                    $searchContent = '';

                    if (!empty($TraverserData['ItemDataEntity'])) {
                        $processors = [
                            'Text',
                            'CustomFields.CustomFields'
                        ];

                        $params = [
                            'item' => $TraverserData['ItemDataEntity'],
                            'field' => $TraverserData['FieldDataEntity']
                        ];

                        $data[$key][$FilterField->getFieldName()] = $View->ObjectRenderer->render('AdvancedFilters.Cell', $params, $processors);
                    }
                }

                $key++;
            }
        }
        
        $_serialize = 'data';
        $this->controller->set(compact('_header', '_extract', 'data', '_serialize'));
    }


    /**
     * sets data for daily data CSV
     * 
     * @param array $filtersData - reconstructed filter results data
     * @deprecated
     */
    private function setDataCsvData2($filtersData, $filter) {
        $model = $filter['AdvancedFilter']['model'];

        // $this->AdvancedFilters->buildRequest($filter['AdvancedFilter']['id'], 'data', $filter['AdvancedFilter']['model']);

        // $this->initAdvancedFilter($model);

        // $data = end($filtersData);
        $data = array();
        foreach ($filtersData as $item) {
            if (empty($item['data'])) {
                continue;
            }
            foreach ($item['data'] as $dataItem) {
                $data[] = $dataItem;
            }
        }
        $this->AdvancedFilters->cronDataCsv($model, $data);
    }

    /**
     * find all data cron results items
     * 
     * @param  int $fiterId
     */
    private function getDataCronData($fiterId) {
        $filterData = $this->controller->AdvancedFilterCronResultItem->find('all', array(
            'conditions' => array(
                'AdvancedFilterCron.advanced_filter_id' => $fiterId,
                'Cron.status' => Cron::STATUS_SUCCESS
            ),
            'contain' => array(
                'AdvancedFilterCron' => array(
                    'Cron'
                )
            ),
            'limit' => AdvancedFilterCron::EXPORT_ROWS_LIMIT,
            'order' => array('AdvancedFilterCronResultItem.id' => 'DESC'),
            'joins' => array(
                array(
                    'table' => 'cron',
                    'alias' => 'Cron',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'AdvancedFilterCron.cron_id = Cron.id'
                    )
                )
            )
        ));

        $resultData = array();
        foreach ($filterData as $item) {
            $cronId = $item['AdvancedFilterCronResultItem']['advanced_filter_cron_id'];
            if (empty($resultData[$cronId]['cron'])) {
                $resultData[$cronId]['cron'] = $item['AdvancedFilterCron'];
            }
            $data = json_decode($item['AdvancedFilterCronResultItem']['data'], true);
            $data['__cron_date'] = $item['AdvancedFilterCron']['date'];
            $resultData[$cronId]['data'][] = $data;
        }

        return $resultData;
    }

}
