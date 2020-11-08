<?php
App::uses('AppHelper', 'View/Helper');
App::Uses('CakeNumber', 'Utility');
App::Uses('CakeTime', 'Utility');
App::uses('CakeSession', 'Model/Datasource');

class UxHelper extends AppHelper {
    public static $infoFlashTimeout = 15000;

    public $helpers = array('Html', 'Text', 'Flash', 'Content', 'FieldData.FieldData');
    public $settings = array();
    
    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->settings = $settings;
    }

    /**
     * Generic method to render default and information flash messages.
     * 
     * @return string Html/JS for the flash messages.
     */
    public function renderFlash() {
        $this->sanitizeObsoleteFlashSession('flash');
        $this->sanitizeObsoleteFlashSession('info');

        $render = [];
        $render[] = $this->Flash->render();

        $render[] = $this->Flash->render('info', [
            'params' => [
                'timeout' => self::$infoFlashTimeout,
                'renderTimeout' => 1500
            ]
        ]);

        return implode('', array_filter($render));
    }

    public function sanitizeObsoleteFlashSession($key) {
        if (CakeSession::check("Message.$key")) {
            $flash = CakeSession::read("Message.$key");
            $val = [];
            foreach ($flash as $key2 => $item) {
                if (!$this->filterNonNumericKeys($key2)) {
                    CakeSession::delete("Message.$key.$key2");
                }
            }
        }
    }

    public function filterNonNumericKeys($arr) {
        if (!is_numeric($arr)) {
            return false;
        }

        return true;
    }

    public function isMissing($date) {
        $today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
        $plannedDate = $date;

        return $plannedDate < $today;
    }

    public function outputCurrency($data, $options = array()) {
        return CakeNumber::currency($data);
    }

    public function outputPercentage($data, $options = array()) {
        return CakeNumber::toPercentage($data, 0);
    }

    public function getItemLink($name, $model, $id) {
        $controller = controllerFromModel($model);

        $link = $this->Html->link($name, array(
            'controller' => $controller,
            'action' => 'index',
            '?' => array(
                'id' => $id,
            )
        ));

        return $link;
    }

    /**
     * Get generic icon html.
     * 
     * @param  string $class   Icon class.
     * @param  array  $options TBD.
     * @return string          HTML.
     */
    public function getIcon($class, $options = array()) {
        $options = am(array(), $options);

        return $this->Html->tag('i', false, array(
            'class' => 'icon icon-' . $class
        ));
    }

    /**
     * Get styled alert box by type.
     * 
     * @param  string $text    Text inside of the box
     * @param  array  $options Options
     * @return string          Generated alert box
     */
    public function getAlert($text, $options = array()) {
        $options = am(array(
            'type' => 'warning',
            'class' => null
        ), $options);

        $class = array('alert', 'alert-' . $options['type']);
        if (!empty($options['class'])) {
            $class = am($class, $options['class']);
        }

        unset($options['type']);
        unset($options['class']);

        return $this->Html->div(
            implode($class, ' '), 
            $this->text($text, [
                'htmlentities' => false
            ]),
            $options
        );
    }

    /**
     * Commonly used output to display separated list of items.
     */
    public function commonListOutput($list, $separate = ', ') {
        return implode($separate, $list);
    }

    /**
     * General method to display a formatted date.
     */
    public function date($data) {
        $date = CakeTime::format(CakeTime::fromString($data), '%Y-%m-%d'); // '%Y-%m-%d %H:%M:%S'
        return $this->text($date);
    }

     /**
     * General method to display a formatted date.
     */
    public function datetime($data) {
        $date = CakeTime::format(CakeTime::fromString($data), '%Y-%m-%d %H:%M:%S');
        return $this->text($date);
    }

    /**
     * @deprecated
     * 
     * Alias for ContentHelper::text()
     */
    public function text($value, $options = [])
    {
        return $this->Content->text($value, $options);
    }

    /**
     * Logout button for header <ul> list.
     */
    public function logoutBtn($url = null) {
        if ($url === null) {
            $url = ['controller' => 'users', 'action' => 'logout', 'admin' => false, 'plugin' => null];
        }

        return $this->Html->link(
            '<i class="icon-switch2"></i> '. __('Log Out'),
            $url,
            array('escape' => false)
        );
    }

    public function createLetterUserPic($username)
    {
        $firstLetter = mb_substr($username, 0, 1);
        $letter = strtoupper($firstLetter);

        $colorLetters = [
            'green' => ['A', 'B', 'C', 'Ç', 'D'],
            'red' => ['E', 'F', 'G', 'Ğ', 'H'],
            'yellow' => ['I', 'J', 'K', 'L'],
            'orange' => ['M', 'N', 'O', 'Ö', 'P'],
            'brown' => ['Q', 'R', 'S', 'Ş', 'T'],
            'purple' => ['U', 'Ü', 'V', 'W', 'X', 'Y', 'Z']
        ];

        $colorClass = 'blue';
        foreach ($colorLetters as $color => $cl) {
            if (in_array($letter, $cl)) {
                $colorClass = $color;
                break;
            }
        }

        return $this->Html->tag('span', $letter, [
            'class' => 'default-user-pic ' . $colorClass
        ]);
    }

    public function handleAuditCalendarRecurrence(FieldDataEntity $Field, array $url)
    {
        return "";
    }

    public function handleAuditCalendarFields(FieldDataEntity $Field, array $url)
    {
        $fieldName = $Field->getFieldName();
        $elementName = $fieldName . '-calendar-wrapper';
        $fieldsClass = $fieldName. '-field-wrapper';

        //
        // Prepare URL
        $urlDefaults = [
            'controller' => null,
            'action' => 'auditCalendarFormEntry',
            'args' => [
                $fieldName
            ]
        ];

        if (!empty($url['args'])) {
            unset($urlDefaults['args']);
        }

        $url = array_merge($urlDefaults, $url);

        foreach ($url['args'] as $arg) {
            $url[] = $arg;
        }
        unset($url['args']);
        //

        $addNew = $this->Html->link(__('Add New'), '#', [
            'id' => 'add_audit_calendar_field',
            'class' => 'btn btn-default',
            'data-yjs-request' => 'crud/addInputField/fieldName::' . $fieldName . '/fieldsClass::' . $fieldsClass,
            'data-yjs-target' => '#' . $elementName,
            'data-yjs-event-on' => 'click',
            'data-yjs-target-placement' => 'append',
            'data-yjs-datasource-url' =>  Router::url($url),
            'data-yjs-lock-request-call' => 'true'
        ]);

        $AssocModel = ClassRegistry::init($fieldName);
        $AssocFieldData = $AssocModel->getFieldCollection();

        $this->_View->set($AssocFieldData->getViewOptions('AuditCalendarCollection'));

        $content = '';
        if (isset($this->request->data[$fieldName])) {
            $i = 0;
            $content = [];
            foreach ($this->request->data[$fieldName] as $audit) {
                $content[] = $this->_View->element('../Risks/audit_calendar_fields', [
                    'fieldsCount' => $i++,
                    'AuditCalendarCollection' => $this->_View->get('AuditCalendarCollection'),
                    'fieldsClass' => $fieldsClass,
                    'fieldName' => $fieldName
                ]);
            }

            $content = implode('', $content);
        }

        $wrapper = $this->Html->div('test', $content, [
            'id' => $elementName
        ]);

        return $this->FieldData->label($Field) . '<br>' . $addNew . '<br><br>' . $wrapper . '' . $this->FieldData->description($Field);
    }
}
