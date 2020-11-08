<?php
App::uses('ModelBehavior', 'Model');
App::uses('MacroCollection', 'Macros.Lib');
App::uses('FieldDataMacroSeed', 'FieldData.Lib/Macros/Seed');

class MacroBehavior extends ModelBehavior
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaults = [
        'prefix' => '',
        'prefixLabel' => null,
        'assoc' => [],
        'seed' => []
    ];

    public $settings = [];

    public $_runtime = [];

    /**
     * Setup.
     *
     * @param Model $Model
     * @param array $settings
     * @throws RuntimeException
     * @return void
     */
    public function setup(Model $Model, $settings = [])
    {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }

        $this->_loadSettings($Model);
    }

    /**
     * Load settings from model.
     * 
     * @param Model $Model
     * @return void
     */
    protected function _loadSettings(Model $Model)
    {
        $config = [];

        if ($Model->hasMethod('getMacrosConfig')) {
            $config = $Model->getMacrosConfig();
        }

        $this->settings[$Model->alias] = Hash::merge($this->settings[$Model->alias], $config);
    }

    /**
     * Init base model macro collection.
     * 
     * @param Model $Model
     * @return void
     */
    protected function _initCollection(Model $Model)
    {
        $Collection = new MacroCollection($Model);

        $FieldDataSeed = new FieldDataMacroSeed($Model->getFieldCollection());
        $Collection->addBySeed($FieldDataSeed);

        foreach ($this->settings[$Model->alias]['assoc'] as $assoc) {
            $AssocModel = $this->_traverseModel($Model, $assoc);

            if ($AssocModel->Behaviors->enabled('FieldData.FieldData')) {
                $FieldDataSeed = new FieldDataMacroSeed($AssocModel->getFieldCollection(), $assoc);
                $Collection->addBySeed($FieldDataSeed);
            }
        }

        $this->_customSeeds($Model, $Collection);

        $this->_runtime[$Model->alias] = $Collection;
    }

    /**
     * Get model macro collection.
     * 
     * @param Model $Model
     * @return MacroCollection $Collection
     */
    protected function _customSeeds(Model $Model, MacroCollection $Collection)
    {
        foreach ($this->settings[$Model->alias]['seed'] as $callable) {
            call_user_func($callable, $Collection);
        }
    }

    /**
     * Helper traverse method.
     */
    protected function _traverseModel(Model $Model, $modelPath = null)
    {
        if (empty($modelPath)) {
            return $Model;
        }

        $path = explode('.', $modelPath);

        foreach ($path as $assoc) {
            $Model = $Model->{$assoc};
        }

        return $Model;
    }

    /**
     * Init model collection if no exists.
     * 
     * @param Model $Model
     * @return void
     */
    protected function _ensureCollection(Model $Model)
    {
        if (!isset($this->_runtime[$Model->alias])) {
            $this->_initCollection($Model);
        }
    }

    /**
     * Get model macro collection.
     * 
     * @param Model $Model
     * @return MacroCollection
     */
    public function getMacroCollection(Model $Model)
    {
        $this->_ensureCollection($Model);

        return $this->_runtime[$Model->alias];
    }

    /**
     * Get model macro collection.
     * 
     * @param Model $Model
     * @return MacroCollection
     */
    public function getMacroByName(Model $Model, $name)
    {
        $Macro = $this->getMacroCollection($Model)->get($this->getMacroAlias($Model, $name));

        return (!empty($Macro)) ? $Macro->macro() : '';
    }

    /**
     * Get model macro prefix.
     * 
     * @param Model $Model
     * @return string
     */
    public function getMacroPrefix(Model $Model)
    {
        $prefix = $this->settings[$Model->alias]['prefix'];

        if (empty($prefix)) {
            $prefix = $Model->name;
        }

        return $this->_sanitizeMacroAlias($prefix);
    }

    /**
     * Get model macro name.
     * 
     * @param Model $Model
     * @return string
     */
    public function getMacroAlias(Model $Model, $name)
    {
        $macro = $Model->getMacroPrefix($Model) . '_' . $name;

        return $this->_sanitizeMacroAlias($macro);
    }

    /**
     * Sanitize macro name.
     * 
     * @param string $name
     * @return string
     */
    public function _sanitizeMacroAlias($name)
    {
        return strtoupper(str_replace(' ', '_', $name));
    }

    /**
     * Get macro group settings for model.
     * 
     * @param Model $Model
     * @param string $key Key of specified setting.
     * @return array|string
     */
    public function getMacroGroupModelSettings($Model, $key = null)
    {
        $settings = [
            'name' => (isset($this->settings[$Model->alias]['prefixLabel'])) ? $this->settings[$Model->alias]['prefixLabel'] : $Model->label(['singular' => true]),
            'slug' => $Model->alias
        ];

        return ($key !== null) ? $settings[$key] : $settings;
    }
}

