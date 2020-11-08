<?php
App::uses('AppHelper', 'View/Helper');
App::uses('UserFields', 'UserFields.Lib');

class UserFieldHelper extends AppHelper {
    public $helpers = [];

    /**
     * Get UserField data separated by comma
     * @param  array $data Data, usually from find() operation where is UserField's data prepared (users and groups together under one UserField)
     * @return array       Prepared data for view
     */
    public function showUserFieldRecords($data)
    {
    	$userFieldRecords = array();
		foreach ($data as $record) {
			if (isset($record['full_name_with_type'])) {
				$userFieldRecords[] = $record['full_name_with_type'];
			}
		}
		
        $displayData = implode(', ', $userFieldRecords);
		return !empty($displayData) ? $displayData : '-';
    }

    /**
     * Get UserField data separated by comma, in case where data are not converted after find() operation - for example if UserField is from associated model
     * @param  string $modelAlias Model's name wher UserField is defined
     * @param  string $field      Name of UserField
     * @param  array $data        Data, usually from find() operation, with UserField's data (in plain format from database, UserField separated from its related associations - UserFieldUser and UserFieldGroup separated)
     * @return array              Prepared data for view
     */
    public function convertAndShowUserFieldRecords($modelAlias, $field, $data)
    {
    	$UserFields = new UserFields();
    	$tempData = [
    		0 => $data
    	];
    	$data = $UserFields->convertDataFromDb($modelAlias, $field, $tempData);

    	return $this->showUserFieldRecords(isset($data[0][$field]) ? $data[0][$field] : []);
    }

    /**
     * Sets default value for UserField's form input from given data
     * @param string  $field           Name of UserField
     * @param array  $data             Prepared data for default attribute of form input
     * @param boolean $useAdminDefault Whether or not to use Admin use like default option when given data are empty
     */
    public function setDefaultValues($field, $data, $useAdminDefault = false)
    {
        $defaultValues = [];
        if (array_key_exists($field, $data)) {
            foreach ($data[$field] as $d) {
                if (isset($d['id'])) {
                    $defaultValues[] = $d['id'];
                }
            }
        }

        if (empty($defaultValues) && $useAdminDefault) {
            $defaultValues[] = 'User-' . ADMIN_ID;
        }

        return $defaultValues;
    }
}
