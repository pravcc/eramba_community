<?php
App::uses('Helper', 'View');

class AclHelper extends Helper
{
    public $helpers = ['Html', 'Js'];

    protected $_roles = [];
    protected $_roleInfo = [
    	'modelName' => null,
    	'primaryKey' => null,
    	'foreignKey' => null
    ];

    protected $_jsInitItems = [];

    public function setRoles($roles)
    {
    	$this->_roles = $roles;
    }

    public function setRoleInfo($roleInfo)
    {
    	$this->_roleInfo = array_merge($this->_roleInfo, $roleInfo);
    }

    public function actionRow($controller, $action, $plugin = '', $labelPrefix = '')
    {
        $out = '';

    	$label = $controller . '/' . ucfirst($action);

        if (!empty($labelPrefix)) {
            $label = $this->Html->tag('span', $labelPrefix, ['class' => 'label bg-grey-400']) . ' ' . $label;
        }

        $out .= $this->Html->tag('td', $label);

    	foreach ($this->_roles as $role) {
    		$roleId = $role[$this->_roleInfo['modelName']][$this->_roleInfo['primaryKey']];

    		// missing aco node script
    		if(!in_array("{$plugin}_{$controller}_{$roleId}", $this->_jsInitItems)) {
    			$this->_jsInitItems[] = "{$plugin}_{$controller}_{$roleId}";
				$this->Js->buffer('init_register_role_controller_toggle_right("' . $this->Html->url('/') . '", "' . $roleId . '", "' . $plugin . '", "' . $controller . '", "' . __d('acl', 'The ACO node is probably missing. Please try to rebuild the ACOs first.') . '");');
			}

			// action wrapper
    		$span = $this->Html->tag('span', '', [
    			'id' => "right_{$plugin}_{$roleId}_{$controller}_{$action}",
                'class' => 'acl-action-options'
    		]);

    		// loader elem
    		$loader = $this->Html->tag('i', ' ', [
    			'class' => 'icon-spinner icon-spin icon-2x acl-action-loader',
    			'id' => "right_{$plugin}_{$roleId}_{$controller}_{$action}_spinner",
    			'style' => 'display:none;'
			]);

            $out .= $this->Html->tag('td', $span . $loader, ['class' => 'acl-action-td']);
    	}

        return $this->Html->tag('tr', $out);
    }

}