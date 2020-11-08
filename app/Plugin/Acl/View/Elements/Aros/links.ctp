<div class="btn-group btn-group-justified">
	<?php
	$selected = isset($selected) ? $selected : $this->params['action'];
	
	//$links = array();
	//$links[] = $this->Html->link(__d('acl', 'Users roles'), '/admin/acl/aros/users', array('class' => ($selected == 'admin_users' )? 'selected' : null));
	
	if(Configure :: read('acl.gui.roles_permissions.ajax') === true)
	{
	    echo $this->Html->link(__d('acl', 'Roles permissions'), '/admin/acl/aros/ajax_role_permissions', array('class' => 'btn btn-default '. ($selected == 'admin_role_permissions' || $selected == 'admin_ajax_role_permissions' ? 'active' : '')));
	}
	else
	{
	    echo $this->Html->link(__d('acl', 'Roles permissions'), '/admin/acl/aros/role_permissions', array('class' => 'btn btn-default '. ($selected == 'admin_role_permissions' || $selected == 'admin_ajax_role_permissions' ? 'active' : '')));
	}
	//$links[] = $this->Html->link(__d('acl', 'Users permissions'), '/admin/acl/aros/user_permissions', array('class' => ($selected == 'admin_user_permissions' )? 'selected' : null));
	echo $this->Html->link(__d('acl', 'Build missing AROs'), '/admin/acl/aros/check', array('class' => 'btn btn-default '. ($selected == 'admin_check' ? 'active' : '')));
	
	//echo $this->Html->nestedList($links, array('class' => 'acl_links'));
	?>
</div>