<div class="row">
	<div class="dataTables_footer clearfix">
		<div class="col-md-6">
			<?php
			// echo $this->Html->tag('button', __('Close'), array('class' => 'btn btn-inverse', 'data-dismiss' => 'modal'));
			echo $this->Ajax->cancelBtn();
			?>
		</div>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_bootstrap">
				<ul class="pagination">
					<?php echo $this->Paginator->prev( __( 'Previous' ), array(
						'tag' => 'li'
					), null, array(
						'class' => 'prev disabled',
						'tag' => 'li',
						'disabledTag' => 'a'
					) ); ?>

					<?php echo $this->Paginator->numbers( array(
						'tag' => 'li',
						'currentClass' => 'active',
						'currentTag' => 'a',
						'separator' => ''
					) ); ?>

					<?php echo $this->Paginator->next( __( 'Next' ), array(
						'tag' => 'li'
					), null, array(
						'class' => 'prev disabled',
						'tag' => 'li',
						'disabledTag' => 'a'
					) ); ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Js->writeBuffer(); ?>