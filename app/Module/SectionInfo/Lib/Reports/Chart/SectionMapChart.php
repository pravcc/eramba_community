<?php
App::uses('AppBaseChart', 'Lib/Reports');
App::uses('Hash', 'Utility');

class SectionMapChart extends AppBaseChart
{
    protected $_nodes = [];
    protected $_data = [];
    protected $_links = [];

    protected $_xStep = 150;
    protected $_yStep = 50;

    protected $_xPos = 0;
    protected $_yPos = 0;

    /**
     * Get default base chart config.
     * 
     * @return array Config.
     */
    protected function _defaultConfig()
    {
        return Hash::merge(parent::_defaultConfig(), [
            'tooltip' => [
                'show' => false
            ],
            'series' => [],
        ]);
    }

    /**
     * Get default series config.
     * 
     * @return array Config.
     */
    protected function _defaultSeriesConfig()
    {
        return [
            'type' => 'graph',
            'layout' => 'none',
            'symbol' => 'roundRect',
            'symbolSize' => 105,
            'label' => [
                'color' => '#000000',
                'show' => true,
                'rich' => [
                    'g' => [
                        'color' => '#ffffff',
                        'fontSize' => 12,
                        'align' => 'center',
                    ],
                    'n' => [
                        'color' => '#ffffff',
                        'align' => 'center',
                        'fontWeight' => 'bold'
                    ]
                ],
                'formatter' => self::jsRaw("function (value) {
                    var out = '{g|' + value.data.group + '}{n|' + value.data.name + '}';

                    if (typeof value.data.count !== 'undefined') {
                        out += '{g|' + value.data.count + '}';
                    }

                    return out;
                }"),
            ],
            'itemStyle' => [
                'color' => '#00ACC1'
            ],
            'emphasis' => [
                'itemStyle' => [
                    'color' => '#00ACC1'
                ],
            ],
            'edgeSymbol' => ['pin', 'pin'],
            'edgeSymbolSize' => [10, 10],
            'edgeLabel' => [
                'normal' => [
                    'textStyle' => [
                        'fontSize' => 20
                    ]
                ]
            ],
            'lineStyle' => [
                'normal' => [
                    'opacity' => 0.9,
                    'width' => 2,
                    'curveness' => 0
                ]
            ],
        ];
    }

    public function map($model, $map, $itemsCount)
    {
        $data = [];

        $this->_initData($model, $map);
        $this->_initLinks(ClassRegistry::init($model), $map);

        $this->_data[count($this->_data)-1]['itemStyle']['color'] = '#F4511E';
        $this->_data[count($this->_data)-1]['emphasis']['itemStyle']['color'] = '#F4511E';
        $this->_data[count($this->_data)-1]['count'] = self::EOL . '(' . $itemsCount . ' ' . __n('item', 'items', $itemsCount) . ')';

        $height = $this->_yPos * 3;
        if ($height < 200) {
            $height = 200;
        }

        $this->settings('height', $height);

        $this->addSeries([
            'data' => $this->_data,
            'links' => $this->_links
        ]);
    }

    protected function _initData($model, $children)
    {
        if (isset($this->_nodes[$model])) {
            return;
        }

        if (empty($children)) {
            $this->_nodes[$model] = count($this->_data);

            $x = $this->_xPos;
            $y = $this->_yPos;

            // debug(ClassRegistry::init($model)->label(['singular' => true]));
            // debug(ClassRegistry::init($model)->group());

            $this->_data[] = [
                'name' => self::breakWords(ClassRegistry::init($model)->label(['singular' => true]), 13, 100),
                'group' => self::breakWords(ClassRegistry::init($model)->group(), 13, 100) . self::EOL . self::EOL,
                'x' => $x,
                'y' => $y,
            ];

            $this->_yPos += $this->_yStep;

            return [$x, $y];
        }

        $childrenPositions = [];

        foreach ($children as $key => $item) {
            $subChildren = (is_array($item)) ? $item : [];
            $subModel = (is_array($item)) ? $key : $item;

            $this->_xPos += $this->_xStep;

            $childrenPositions[] = $this->_initData($subModel, $subChildren);

            $this->_xPos -= $this->_xStep;
        }

        $this->_nodes[$model] = count($this->_data);

        $x = $this->_xPos;
        $y = ($childrenPositions[0][1] + $childrenPositions[count($childrenPositions)-1][1]) / 2;

        $this->_data[] = [
            'name' => self::breakWords(ClassRegistry::init($model)->label(['singular' => true]), 13, 100),
            'group' => self::breakWords(ClassRegistry::init($model)->group(), 13, 100) . self::EOL . self::EOL,
            'x' => $x,
            'y' => $y,
        ];

        return [$x, $y];
    }

    protected function _initLinks($Model, $children)
    {
        foreach ($children as $key => $item) {
            $subChildren = (is_array($item)) ? $item : [];
            $subModel = (is_array($item)) ? $key : $item;

            $SubModel = ClassRegistry::init($subModel);

            $this->_initLinks($SubModel, $subChildren);

            $this->_links[] = [
                'source' => $this->_nodes[$Model->alias],
                'target' => $this->_nodes[$SubModel->alias],
                'label' => [
                    'show' => true,
                    'formatter' => ($this->_isMandatory($Model, $subModel) || $this->_isMandatory($SubModel, $Model->alias)) ? __('Mandatory') : __('Optional'),
                    'fontSize' => 13,
                ]
            ];
        }
    }

    protected function _getModelLabel($Model)
    {
        return self::breakWords($Model->label(['singular' => true]) . " ({$Model->group()})", 13, 100);
    }

    protected function _isMandatory($Model, $assoc)
    {
        $mandatory = false;

        if (empty($Model->getAssociated($assoc))) {
            return false;
        }

        $assocData = $Model->getAssociated($assoc);

        $field = ($assocData['association'] == 'belongsTo') ? $assocData['foreignKey'] : $assoc;

        if (!empty($Model->validate[$field])) {
            $mandatory = true;
        }

        return $mandatory;
    }
}
