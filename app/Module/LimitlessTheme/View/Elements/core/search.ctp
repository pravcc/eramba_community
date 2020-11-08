<?php echo $this->Form->create('Search', array(
	'url' => array('controller' => $this->params['controller'], 'action' => 'index'),
	'type' => 'get'
)); ?>
<div class="input-group">
	<?php echo $this->Form->input( 'search', array(
		'label' => false,
		'div' => false,
		'class' => 'form-control',
		'default' => isset($this->request->query['search']) ? $this->request->query['search'] : false
	) ); ?>
	<span class="input-group-btn">
		<?php echo $this->Form->button('<i class="icon icon-search"></i>', array(
			'type' => 'submit',
			'class' => 'btn btn-default',
			'escape' => false
		)); ?>
	</span>
</div>
<?php echo $this->Form->end(); ?>