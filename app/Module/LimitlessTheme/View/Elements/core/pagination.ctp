<div class="row">
	<div class="dataTables_footer clearfix">
		<div class="col-md-6">
			<?php
			if (isset($url) && !empty($url)) {
				echo $this->Html->link(__('Back'), $url, array(
					'class' => 'btn btn-inverse'
				));
			}
			?>
		</div>
		<div class="col-md-6">
			<?php echo $this->element(CORE_ELEMENT_PATH . 'pagination_numbers' ); ?>
		</div>
	</div>
</div>