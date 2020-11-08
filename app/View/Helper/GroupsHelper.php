<?php
App::uses('AppHelper', 'View/Helper');
class GroupsHelper extends AppHelper {
	public $settings = array();
	public $helpers = ['Ux'];
	

	/**
     * Get list of group names from data array.
     * 
     * @return array List of names.
     */
    public function getNameList($data, $modelAlias = 'Group') {
        $list = array();
        foreach ($data[$modelAlias] as $item) {
            $list[] = $item['name'];
        }

        return $list;
    }

    public function listNames($data, $modelAlias = 'Group') {
    	$list = $this->getNameList($data, $modelAlias);

    	return $this->Ux->commonListOutput($list);
    }

}