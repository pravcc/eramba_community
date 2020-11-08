<?php
App::uses('AdvancedFilterCron', 'Model');

class AdvancedFilterCronResultItem extends AppModel {

    public $belongsTo = array('AdvancedFilterCron');

    /**
     * Save result item record, encodes raw data to JSON string.
     */
    public function saveResultItem($filterCronId, $dataRaw) {
        $this->create();
        $data = json_encode($dataRaw);
        $this->set(array(
            'advanced_filter_cron_id' => $filterCronId,
            'data' => $data,
        ));
        return $this->save(null, false);
    }

    /**
     * Remove obsolete filter items.
     * 
     * @param  int $filterId AdvancedFilter.id
     * @param  int $offset Number of items we want to let.
     * @return boolean Success.
     */
    public function removeObsoleteItems($filterId, $offset = null) {
        if ($offset === null) {
            $offset = AdvancedFilterCron::EXPORT_ROWS_LIMIT;
        }

        $data = $this->find('first', [
            'conditions' => [
                'AdvancedFilterCron.advanced_filter_id' => $filterId,
            ],
            'order' => ['AdvancedFilterCronResultItem.id' => 'DESC'],
            'contain' => ['AdvancedFilterCron'],
            'offset' => $offset
        ]);

        if (empty($data)) {
            return true;
        }

        return $this->deleteAll([
            'AdvancedFilterCronResultItem.id <=' => $data['AdvancedFilterCronResultItem']['id'],
            'AdvancedFilterCron.advanced_filter_id' => $filterId,
        ]);
    }

    public $virtualFields = array(
        'date' => 'DATE(AdvancedFilterCronResultItem.created)',
    );
}
