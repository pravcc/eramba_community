<div class="btn-group btn-group-justified">
	<?php
	$selected = isset($selected) ? $selected : $this->params['action'];
	
	//$links = array();
	echo $this->Html->link(__d('acl', 'Synchronize actions ACOs'), '/admin/acl/acos/synchronize', array(array('confirm' => __d('acl', 'are you sure ?')), 'class' => 'btn btn-default '. ($selected == 'admin_synchronize' ? 'active' : '')));
	//echo $this->Html->link(__d('acl', 'Clear actions ACOs'),       '/admin/acl/acos/empty_acos',  array(array('confirm' => __d('acl', 'are you sure ?')), 'class' => 'btn '. ($selected == 'admin_empty_acos'  ? 'active' : '')));
	//echo $this->Html->link(__d('acl', 'Build actions ACOs'),       '/admin/acl/acos/build_acl',                                                     array('class' => 'btn '. ($selected == 'admin_build_acl'   ? 'active' : '')));
	//echo $this->Html->link(__d('acl', 'Prune actions ACOs'),       '/admin/acl/acos/prune_acos',  array(array('confirm' => __d('acl', 'are you sure ?')), 'class' => 'btn '. ($selected == 'admin_prune_acos'  ? 'active' : '')));
	
	//echo $this->Html->nestedList($links, array('class' => 'acl_links'));
	?>
</div>