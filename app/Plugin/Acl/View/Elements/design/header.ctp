<?php
echo $this->Html->css('/acl/css/acl.css');
?>
<div class="row" id="plugin_acl">
	<div class="col-md-12">
		<?php
		echo $this->Session->flash('plugin_acl');
		?>
		
		<?php
		
		if(!isset($no_acl_links)) :
		    $selected = isset($selected) ? $selected : $this->params['controller'];
		    ?>
		    <div class="btn-group btn-group-justified">
				<?php
				echo $this->Html->link(__d('acl', 'Permissions'), '/admin/acl/aros/ajax_role_permissions', array('class' => 'btn bg-slate-700 '. ($selected == 'aros' ? 'active' : '')));
				echo $this->Html->link(__d('acl', 'Actions'), '/admin/acl/acos/index', array('class' => 'btn bg-slate-700 '. ($selected == 'acos' ? 'active' : '')));
				?>
			</div>
			<br/>
		<?php endif; ?>


