<div class="row">
    <div class="col-md-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <?php
                echo $this->element('design/header');
                ?>

                <?php
                echo $this->element('Acos/links');
                ?>

                <?php
                if($run)
                {
                    echo '<h3>' . __d('acl', 'New ACOs') . '</h3>';
                    
                    if(count($create_logs) > 0)
                    {
                //        echo '<p>';
                //        echo __d('acl', 'The following actions ACOs have been created');
                //        echo '<p>';
                        echo $this->Html->nestedList($create_logs);
                    }
                    else
                    {
                        echo '<p>';
                        echo __d('acl', 'There was no new actions ACOs to create');
                        echo '</p>';
                    }
                    
                    echo '<h3>' . __d('acl', 'Obsolete ACOs') . '</h3>';
                    
                    if(count($prune_logs) > 0)
                    {
                //        echo '<p>';
                //        echo __d('acl', 'The following actions ACOs have been deleted');
                //        echo '<p>';
                        echo $this->Html->nestedList($prune_logs);
                    }
                    else
                    {
                        echo '<p>';
                        echo __d('acl', 'There was no action ACO to delete');
                        echo '</p>';
                    }
                }
                else
                {
                    echo '<br>';
                    echo $this->Alerts->info(__d('acl', 'This page allows you to synchronize the existing controllers and actions with the ACO datatable.'));

                    if(count($missing_aco_nodes) > 0 || count($nodes_to_prune) > 0)
                    {   
                        echo '<p mt-20>';
                        echo $this->Html->link('<i class="icon-sync icon-green icon-2x"></i> ' . __d('acl', 'Synchronize'), '/admin/acl/acos/synchronize/run', [
                            'class' => 'btn btn-default',
                            'escape' => false
                        ]);
                        echo '</p>';

                        echo '<p>';
                        echo __d('acl', 'Clicking the link will not change or remove permissions for existing actions ACOs.');
                        echo '</p>';
                    }
                    else
                    {
                        echo '<p style="font-style:italic;">';
                        echo '<i class="icon-checkmark5 icon-green icon-2x"></i> ' . __d('acl', 'The ACO datatable is already synchronized');
                        echo '</p>';
                    }
                    
                    if(count($missing_aco_nodes) > 0)
                    {
                        echo '<h3>' . __d('acl', 'Missing ACOs') . '</h3>';
                        
                        echo '<p>';
                    	echo $this->Html->nestedList($missing_aco_nodes, [], [
                            '']);
                    	echo '</p>';
                    }
                    
                    if(count($nodes_to_prune) > 0)
                    {
                        echo '<h3>' . __d('acl', 'Obsolete ACO nodes') . '</h3>';
                	    
                	    echo '<p>';
                    	echo $this->Html->nestedList($nodes_to_prune);
                    	echo '</p>';
                    }
                }

                echo $this->element('design/footer');
                ?>
            </div>
        </div>
    </div>
</div>
