<?php
App::uses('AppShell', 'Console/Command');
App::uses('ImportToolCsv', 'ImportTool.Lib');
App::uses('ImportToolData', 'ImportTool.Lib');
App::uses('ConnectionManager', 'Model');
// App::uses('CakeRequest', 'Network');

/**
 * @deprecated
 */
class ImportShell extends AppShell {
    // public $uses = array('Setting');

    public function startup()
    {
        parent::startup();
    }

    public function main()
    {   
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        if (!isset($this->args[0])) {
            $this->error('Please provide model');
        }
        if (!isset($this->args[1])) {
            $this->error('Please provide csv file');
        }

        $modelName = $this->args[0];
        $fileName = $this->args[1];
        
        $data = $this->_readCsvFile(__DIR__ . DS . $fileName);

        $model = ClassRegistry::init($modelName);

        if ($modelName == 'BusinessUnit') {
            $model->importArgs = array(
                'name' => array(
                    'name' => __('Name')
                ),
                'description' => array(
                    'name' => __('Description')
                ),
                'business_unit_owner_id' => array(
                    'name' => __('Business Unit Owner'),
                    'model' => 'BusinessUnitOwner',
                ),
                'legal_id' => array(
                    'name' => __('Liabilities'),
                    'model' => 'Legal'
                ),
            );
        }

        $ImportToolData = new ImportToolData($model, $data);

        $saveData = $ImportToolData->getImportableDataArray();
        $saveData = $this->_formatAssociatedData($saveData, $model);
// debug($saveData);exit;
        $ds = ConnectionManager::getDataSource('default');

        // $_prevDebugCfg = $ds->fullDebug;
        $this->out('Importing...');
        $ds->begin();
        // $ds->getLog();
        $ds->fullDebug = true;

        $ret = $model->saveMany($saveData);

        if ($ret) {
            $ds->commit();
            $this->out(sprintf('Import successful, %s items created.', count($saveData)));
        }
        else {
            $ds->rollback();
            $this->error('Error occured, please try again.');
        }
        // $ds->fullDebug = false;
        $ds->rollback();
        // $ds->fullDebug = $_prevDebugCfg;

        // $this->_printSqlLog($ds->getLog());
    }

    private function _readCsvFile($file)
    {
        $csv = new ImportToolCsv($file);

        $errors = $csv->getErrors();
        if (!empty($errors)) {
            $this->error($errors);
        }

        return $csv->getData();
    }

    private function _formatAssociatedData($data, $model)
    {
        $importArgs = $model->importArgs;
        $formatedData = array();

        foreach ($data as $key => $item) {
            $fomatedItem = array();

            foreach ($item as $field => $value) {
                if (!empty($importArgs[$field]['model']) && $importArgs[$field]['model'] !== $model->alias) {
                    $fomatedItem[$importArgs[$field]['model']][$importArgs[$field]['model']] = explode(',', $value[0]);
                }
                else {
                    $fomatedItem['BusinessUnit'][$field] = $value;
                }
            }

            $fomatedItem['BusinessUnit']['workflow_owner_id'] = ADMIN_ID;
            $formatedData[] = $fomatedItem;
        }

        return $formatedData;
    }

    private function _printSqlLog($log)
    {
        foreach ($log['log'] as $k => $i) {
            $i += array('error' => '');
            if (!empty($i['params']) && is_array($i['params'])) {
                $bindParam = $bindType = null;
                if (preg_match('/.+ :.+/', $i['query'])) {
                    $bindType = true;
                }
                foreach ($i['params'] as $bindKey => $bindVal) {
                    if ($bindType === true) {
                        $bindParam .= h($bindKey) . " => " . h($bindVal) . ", ";
                    } else {
                        $bindParam .= h($bindVal) . ", ";
                    }
                }
                $i['query'] .= " , params[ " . rtrim($bindParam, ', ') . " ]";
            }
            $this->out($i['query']);
        }
    }
}