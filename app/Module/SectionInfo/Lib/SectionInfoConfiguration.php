<?php
class SectionInfoConfiguration
{
    /**
     * Subject model instance.
     * 
     * @var Model
     */
    protected $_model = null;


    protected $_map = null;

    public function __construct($model)
    {
        $this->_model = $model;
    }

    public function map()
    {
        [
            'objects' => [
                'Legal',
                'Risk',
                'Asset',
                'Tralala'
            ],
        ];
    }



}