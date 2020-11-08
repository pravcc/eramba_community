
<div class="panel panel-flat">
    <div class="panel-body">
		<?php
		echo $this->element('design/header', array('no_acl_links' => true));
		?>

		<div class="error">
			
			<?php
			echo $this->Alerts->danger(__d('acl', 'We noticed incomplete ACLs - please click on "Synchronize ACOs" to correct the issue'));
			
			echo '<p class="mt-10">';
			echo __d('acl', 'Link') . ': ';
			echo $this->Html->link(__d('acl', 'Synchronize ACOs'), '/admin/acl/acos/synchronize/run');
			echo '</p>';
			
			echo '<p class="text-semibold">';
			echo __d('acl', 'Please be aware that this message will appear only once. But you can always rebuild the ACOs by going to the ACO tab.');
			echo '</p>';

			if(count($missing_aco_nodes) > 0)
			{
			    echo '<h3>' . __d('acl', 'Missing ACOs') . '</h3>';
			
		    	echo '<p>';
		    	echo $this->Html->nestedList($missing_aco_nodes);
		    	echo '</p>';
			}
			
			if(count($nodes_to_prune) > 0)
			{
			    echo '<h3>' . __d('acl', 'Obsolete ACOs') . '</h3>';
			    
			    echo '<p>';
		    	echo $this->Html->nestedList($nodes_to_prune);
		    	echo '</p>';
			}
			?>
			
		</div>

		<?php
		echo $this->element('design/footer');
		?>
	</div>
</div>
