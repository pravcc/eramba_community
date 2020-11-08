<?php
App::uses('AppModel', 'Model');
App::uses('AdvancedFilter', 'Model');

class AdvancedFilterUserSetting extends AppModel {

    const DEFAULT_INDEX = 1;
    const NOT_DEFAULT_INDEX = 0;

    public $actsAs = array(
        'Containable',
        'HtmlPurifier.HtmlPurifier' => array(
            'config' => 'Strict',
            'fields' => array(
            )
        ),
    );

    public $belongsTo = array('AdvancedFilter');
    
}