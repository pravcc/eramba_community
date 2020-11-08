<?php
App::uses('AppHelper', 'View/Helper');
App::Uses('CakeNumber', 'Utility');
App::Uses('CakeTime', 'Utility');
App::uses('CakeSession', 'Model/Datasource');

class LabelHelper extends AppHelper {
    public $helpers = array('Html', 'Text');
    public $settings = array();

    /**
     * Map class names to correct labels.
     * 
     * @var array
     */
    public $mapLabels = [
        'error' => 'danger'
    ];
    
    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->settings = $settings;

        $this->mapLabels = [
            'error' => 'danger'
        ];
    }

    /**
     * Parse options for labels.
     * 
     * @param  array $options Options, @see HtmlHelper::tag() method
     * @return Array of options prepared for rendering via HtmlHelper.
     */
    protected function _parseOptions($options) {
        return array_merge([
            'escape' => false
        ], $options);
    }

    /**
     * Magic method that renders a correct label tag.
     * 
     * @param  string $type Type of the label.
     * @param  array $args  Arguments.
     * @return string       Rendered flash message.
     */
    public function __call($type, $args) {
        if (count($args) < 1) {
            throw new InternalErrorException('Flash message missing.');
        }

        $text = array_shift($args);

        return $this->render($type, $text, $args);
    }

    /**
     * Render a label with a success message.
     *   
     * @param  string $type    The type of the label to use
     * @param  string $text    Text you want to include with the label
     * @param  array  $options Optional options
     * @return string          Rendered label
     */
    public function render($type, $text, $options = []) {
        if (isset($this->mapLabels[$type])) {
            $type = $this->mapLabels[$type];
        }

        $options['class'] = 'label label-' . $type;
        $options = $this->_parseOptions($options);

        return $this->Html->tag('span', $text, $options);
    }

}