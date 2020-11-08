<?php
App::uses('AppHelper', 'View/Helper');

class ContentHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Html', 'Text', 'LimitlessTheme.Popovers'];

    protected $_purifierInstance = [];

    public function __construct(View $View, $settings = array())
    {
        parent::__construct($View, $settings);

        $this->_initHtmlPurifierInstances();
    }

    private function _initHtmlPurifierInstances()
    {
        // strict
        $strictPurifierConfig = HTMLPurifier_Config::createDefault();
        $strictPurifierConfig->set('HTML.AllowedElements', '');
        $strictPurifierConfig->set('HTML.AllowedAttributes', '');
        $strictPurifierConfig->set('CSS.AllowedProperties', '');

        $this->_purifierInstance['strict'] = new HTMLPurifier($strictPurifierConfig);

        // light - allow base html
        $lightPurifierConfig = HTMLPurifier_Config::createDefault();
        $lightPurifierConfig->set('HTML.AllowedElements', 'span, p, a, ul, ol, li, h1, h2, h3, h4, h5, h6, br, strong, em, b, i, u, table, tr, th, td, tbody, thead, blockquote, pre, font');
        $lightPurifierConfig->set('HTML.AllowedAttributes', 'style, color, href, target');
        $lightPurifierConfig->set('CSS.AllowedProperties', 'background-color, color');

        $this->_purifierInstance['light'] = new HTMLPurifier($lightPurifierConfig);
    }

	/**
	 * Truncate long text.
	 * 
	 * @param string $text Text to truncate.    
	 * @param int $length Max text length.
	 * @param array $options Additional options.
	 * @return string Truncated text.
	 */
	public function truncate($text, $length, $options = [])
	{
		$options = array_merge([
			'popover' => true,
		], $options);

		if (strlen($text) <= $length) {
			return $text;
		}

		$truncated = $this->Text->truncate($text, $length, $options);

		if ($options['popover']) {
			$truncated = $this->Popovers->top($truncated, $text);
		}

		return $truncated;
	}

	/**
     * General wrapper method to display text.
     */
    public function text($value, $options = [])
    {
    	//cast value to string
    	$value = (string) $value;

        // fallback for previous used second argument $emptySign = '-'
        if (is_string($options)) {
            $options = [
                'emptySign' => $options
            ];
        }

        // options for the text output
        $options = am(array(
            'emptySign' => '-',
            'htmlentities' => false
        ), $options);

        if (is_string($value) && ($value !== '' || $value !== null)) {
            if ($options['htmlentities'] === true) {
                $value = h($value);
            }

            $value = trim($value);
            $value = nl2br($value);
        } else {
            $value = $options['emptySign'];
        }

        return $value;
    }

    /**
     * Sanitize output.
     * 
     * @param string $text
     * @param string $config HtmlPurifier instance name.
     * @return string
     */
    public function purify($text, $config = 'strict')
    {
        return $this->_purifierInstance[$config]->purify($text);
    }

}