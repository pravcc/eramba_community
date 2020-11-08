<li>
	<div class="col1">
		<div class="content">
			<div class="content-col1">
				<div class="label" style="background-color:<?php echo $color; ?>">
					<i class="icon-info"></i>
				</div>
			</div>
			<div class="content-col2">
				<?php if (isset($url) && $url) : ?>
					<div class="desc"><a href="<?php echo $url; ?>"><?php echo $title; ?></a></div>
				<?php else: ?>
					<div class="desc"><?php echo $title; ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div> <!-- /.col1 -->
	<div class="col2">
		<div class="date">
			<?php
			if ( $time ) {
				echo $time;
			}
			?>
		</div>
	</div> <!-- /.col2 -->
</li>