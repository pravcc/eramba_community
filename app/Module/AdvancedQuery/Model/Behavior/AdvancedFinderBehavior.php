<?php
App::uses('ModelBehavior', 'Model');
App::uses('AdvancedQueryBuilder', 'AdvancedQuery.Lib');

/**
 * Advanced Query Finder Behavior
 */
class AdvancedFinderBehavior extends ModelBehavior {

/**
 * Default config
 *
 * @var array
 */
    protected $_defaults = [];

    public $settings = [];

/**
 * Setup
 *
 * @param Model $Model
 * @param array $settings
 * @throws RuntimeException
 * @return void
 */
    public function setup(Model $Model, $settings = []) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }
    }

/**
 * Create instance of AdvancedQueryBuilder.
 * 
 * @param String|Model $model Model instance.
 * @param string $finder Finder type.
 * @param array $options Query options.
 * @return AdvancedQueryBuilder instance.
 */
    public function advancedFind($Model, $finder = 'all', $options = []) {
        return new AdvancedQueryBuilder($Model, $finder, $options);
    }

    public function containList($Model, $nestingLevel = 2)
    {
        $contain = [];

        foreach ($Model->getAssociated() as $assoc => $type) {
            $assocContain = [];

            if ($nestingLevel > 1) {
                $assocContain = $this->containList($Model->{$assoc}, $nestingLevel - 1);
            }

            if (!empty($assocContain)) {
                $contain[$assoc] = $assocContain;
            }
            else {
                $contain[] = $assoc;
            }
        }

        return $contain;
    }
}
