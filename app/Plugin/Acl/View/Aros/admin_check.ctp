<div class="row">
    <div class="col-md-12">
        <div class="panel panel-flat">
            <div class="panel-body">
				<?php
				echo $this->element('design/header');
				?>

				<?php
				echo $this->element('Aros/links');
				?>

				<?php
				if(count($missing_aros['roles']) > 0)
				{
					echo '<h3>' . __d('acl', 'Roles without corresponding Aro') . '</h3>';
					
					$list = array();
					foreach($missing_aros['roles'] as $missing_aro)
					{
						$list[] = $missing_aro[$role_model_name][$role_display_field];
					}
					
					echo $this->Html->nestedList($list);
				}
				?>

				<?php
				if(count($missing_aros['users']) > 0)
				{
					echo '<h3>' . __d('acl', 'Users without corresponding Aro') . '</h3>';
					
					$list = array();
					foreach($missing_aros['users'] as $missing_aro)
					{
						$list[] = $missing_aro[$user_model_name][$user_display_field];
					}
					
					echo $this->Html->nestedList($list);
				}
				?>

				<div class="separator"></div>

				<?php
				if(count($missing_aros['roles']) > 0 || count($missing_aros['users']) > 0)
				{
					echo '<div>';
					echo $this->Html->link(__d('acl', 'Build'), '/admin/acl/aros/check/run', [
						'class' => 'btn btn-default'
					]);
					echo '</div>';
				}
				else
				{
					echo '<div class="alert alert-info">';
					echo __d('acl', 'There is no missing ARO.');
					echo '</div>';
				}
				?>

				<?php
				echo $this->element('design/footer');
				?>
			</div>
		</div>
	</div>
</div>