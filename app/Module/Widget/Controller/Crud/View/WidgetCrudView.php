<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');

class WidgetCrudView extends CrudView
{
    public $name = 'Widget';

    protected $_isModalRequest = false;

    protected $_subjectHash = null;
    protected $_subjectModel = null;
    protected $_subjectForeignKey = null;

    public function subjectHash($hash = null)
    {
        if ($hash !== null) {
            $this->_subjectHash = $hash;
        }

        return $this->_subjectHash;
    }

    public function subjectModel($model = null)
    {
        if ($model !== null) {
            $this->_subjectModel = $model;
        }

        return $this->_subjectModel;
    }

    public function subjectForeignKey($foreignKey = null)
    {
        if ($foreignKey !== null) {
            $this->_subjectForeignKey = $foreignKey;
        }

        return $this->_subjectForeignKey;
    }

    public function extendUrlParamsBySubject($url)
    {
        $hash = $this->subjectHash();
        $model = $this->subjectModel();
        $foreignKey = $this->subjectForeignKey();

        if (!empty($hash)) {
            $url[] = $hash;
        }
        else {
            $url[] = $model;
            $url[] = $foreignKey;
        }

        return $url;
    }

    public function isModalRequest($isModalRequest = null)
    {
        if ($isModalRequest !== null) {
            $this->_isModalRequest = $isModalRequest;
        }

        return $this->_isModalRequest;
    }
}
