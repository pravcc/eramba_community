<?php
App::uses('CakeText', 'Utility');
App::uses('Hash', 'Utility');

class AppBaseChart
{
    const JS_RAW = '--';
    const EOL = '-EOL-';

    /**
     * Unique chart ID.
     * 
     * @var string
     */
    protected $_id = null;

    /**
     * View object.
     * 
     * @var View
     */
    protected $_View = null;

    /**
     * Chart config for chart lib.
     * 
     * @var array
     */
    protected $_config = [];

    /**
     * General settings.
     * 
     * @var array
     */
    protected $_settings = [
        'beforeChart' => '',
        'afterChart' => '',
        'height' => 420,
        'width' => null
    ];

    /**
     * Construnct class and assign base values.
     * 
     * @param View $View
     */
    public function __construct($View)
    {
        $this->_View = $View;

        $this->_id = CakeText::uuid();

        $this->_config = $this->_defaultConfig();
    }

    /**
     * Get default base chart config.
     * 
     * @return array Config.
     */
    protected function _defaultConfig()
    {
        return [
            'tooltip' => [
                'backgroundColor' => 'rgba(0,0,0,0.8)',
                'padding' => [8, 12, 8, 12],
                'axisPointer' => [
                    'type' => 'line',
                    'lineStyle' => [
                        'color' => '#607D8B',
                        'width' => 1
                    ],
                    'crossStyle' => [
                        'color' => '#607D8B'
                    ],
                    'shadowStyle' => [
                        'color' => 'rgba(200,200,200,0.2)'
                    ]
                ],
                'textStyle' => [
                    'fontFamily' => 'Roboto, sans-serif'
                ],
            ],
            'color' => [
                '#2ec7c9','#b6a2de','#5ab1ef','#ffb980','#d87a80',
                '#8d98b3','#e5cf0d','#97b552','#95706d','#dc69aa',
                '#07a2a4','#9a7fd1','#588dd5','#f5994e','#c05050',
                '#59678c','#c9ab00','#7eb00a','#6f5553','#c14089'
            ],
            'animation' => false,
            'settings' => [
                'height' => 420
            ],
            'series' => []
        ];
    }

    /**
     * Get default series config.
     * 
     * @return array Config.
     */
    protected function _defaultSeriesConfig()
    {
        return [];
    }

    /**
     * Update config.
     * 
     * @param string $path Path of config.
     * @param mixed $value Value of config.
     * @return void
     */
    public function config($path, $value)
    {
        $this->_config = Hash::insert($this->_config, $path, $value);
    }

    /**
     * Update settings.
     * 
     * @param string $path Path of settings.
     * @param mixed $value Value of settings.
     * @return void
     */
    public function settings($path, $value)
    {
        $this->_settings = Hash::insert($this->_settings, $path, $value);
    }

    /**
     * Adding of new series config.
     * 
     * @param array $config Series config.
     */
    public function addSeries($config = [])
    {
        $config = Hash::merge($this->_defaultSeriesConfig(), $config);

        $series = $this->_config['series'];

        $series[] = $config;

        $this->config('series', $series);
    }

    /**
     * Render of chart.
     * 
     * @return string Html of chart with init script.
     */
    public function render()
    {
        $chartId = $this->_id;

        // we need to wrap chart in element
        $holderElem = $this->_View->Html->div('report-chart-holder', $this->_chartElement());

        // transform our config array to json string
        $chartConfig = $this->_configToJson($this->_config);

        $script = $this->_View->Html->scriptBlock("
            (function() {
                var chartOptions = {$chartConfig};

                setTimeout(function() {
                    var chart = echarts.init(document.getElementById('{$chartId}'), null, {renderer: 'canvas'});
                    chart.setOption(chartOptions);
                }, 100);
            })();
        ");

        $out = $holderElem . $script;

        return $out;
    }

    /**
     * Get chart element with applied settings.
     * 
     * @return string Html element of chart.
     */
    protected function _chartElement()
    {
        $chartId = $this->_id;

        $styles = [
            sprintf('height: %spx', $this->_settings['height'])
        ];

        if ($this->_settings['width'] !== null) {
            $styles[] = sprintf('width: %spx', $this->_settings['width']);
        }

        $element = $this->_View->Html->div('', '', [
            'id' => $chartId,
            'style' => implode('; ', $styles)
        ]);

        return $this->_settings['beforeChart'] . $element . $this->_settings['afterChart'];
    }

    /**
     * Encode config array to json string.
     * 
     * @param array $config Config.
     * @return string Config in json format.
     */
    protected function _configToJson($config)
    {
        $config = $this->_sanitizeStrings($config);

        $json = json_encode($config, JSON_UNESCAPED_SLASHES);

        // fix special chars
        $encoding = ini_get('mbstring.internal_encoding');
        $json = preg_replace_callback('/\\\\u([0-9a-f]{4})/iu', function($match) use ($encoding) {
            return mb_convert_encoding(pack('H*', $match[1]), $encoding, 'UTF-16BE');
        }, $json);

        $json = str_replace(self::JS_RAW . '"', '', $json);
        $json = str_replace('"' . self::JS_RAW, '', $json);
        $json = str_replace(["\\r\\n", "\\n", "\\r", "\\t"], ' ', $json);
        $json = str_replace(["\\"], ' ', $json);

        $json = str_replace(self::EOL, '\n', $json);

        return $json;
    }

    /**
     * Sanitize all strings in array.
     * 
     * @param array $array.
     * @return array Sanitized array.
     */
    protected function _sanitizeStrings(&$array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->_sanitizeStrings($value);
            }
            elseif (is_string($value) && strpos($value, self::JS_RAW) === false) {
                // sanitize
                $value = str_replace('"', '', $value);
            }

            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * Update chart configuration according to subject data.
     * 
     * @param $subject Subject.
     * @return void
     */
    public function setData($subject)
    {
    }

    /**
     * Set demo configuration.
     * 
     * @return void
     */
    public function setDemoData()
    {
    }

    /**
     * Helper function to wrap raw js code.
     * 
     * @param string $content Content to wrap.
     * @return string Wrapped content.
     */
    public function jsRaw($content)
    {
        return self::JS_RAW . $content . self::JS_RAW;
    }

    /**
     * Helper function to break lines in text label.
     * 
     * @param string $text
     * @param integer $length Line max length.
     * @param boolean $truncate Truncate length, false to not truncate.
     * @return string
     */
    public static function breakWords($text, $length = 30, $truncate = false)
    {
        if (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::breakWords($value, $length, $truncate);
            }
        }
        else {
            if ($truncate !== false) {
                $text = CakeText::truncate($text, $truncate);
            }

            $text = wordwrap($text, $length, self::EOL);
        }

        return $text;
    }

    /**
     * Helper function to format number.
     * 
     * @param float|int $number
     * @param integer $decimalPlaces Count of decimal places.
     * @return float|int
     */
    public static function formatNumber($number, $decimalPlaces = 2)
    {
        if (floor($number) != $number) {
            $number = number_format($number, $decimalPlaces, '.', '');
        }

        return $number;
    }
    
}
