<script>
	(function(window)
	{
		$('#velocity-btn-test').on('mouseover', function (e) {
			// Add animation class to panel element
			$(this).velocity("callout.swing", { stagger: 500, duration: 500 });
			e.preventDefault();
		});

		var gotitBtn = 
		$('#velocity-btn-test').popover({
			html: true,
			title: '<div>Popover title</div>',
			//content: 'And here\'s some amazing content. It\'s very engaging. Right?',
			content: '<div class="content-body">Here we can show any popover text you want</div>' +
					'<div class="text-right mt-20">' +
						'<button class="btn btn-link btn-xs gotit-btn">Got It!</button>' +
						'<button class="btn bg-grey-800 btn-xs show-more-btn">Show me more!</button>' +
					'</div>',
			template: '<div class="popover popover-custom">' +
							'<div class="arrow"></div>' +
							'<h3 class="popover-title bg-primary"></h3>' + 
							'<div class="popover-content"></div>' + 
							'</div>',
			trigger: 'click',
			placement: 'left'
		}).on('inserted.bs.popover', function()
		{
			var
				popover = $(this).data('bs.popover'),
				$element = popover.$element,
				$tip = popover.$tip;
			$tip.find('.gotit-btn').on('click', function()
			{
				$element.popover('hide');
			});

			$tip.find('.show-more-btn').on('click', function()
			{
				$element.popover('hide');

				$(this).data('yjs-request', 'crud/showTooltip');
				$(this).data('yjs-target', 'modal');
				//$(this).data('yjs-datasource-url', 'sectionItems/add');
				var YoonityJSObject = new YoonityJS.Init({
					object: this
				});
			});
		}).on('hidden.bs.popover', function()
		{
			$(this).popover('disable');
			$(this).off('mouseover');

			$(this).data('yjs-request', 'crud/showForm');
			$(this).data('yjs-target', 'modal');
			$(this).data('yjs-datasource-url', 'legals/add');
			$(this).data('yjs-event-on', 'click');
			var YoonityJSObject = new YoonityJS.InitElement({
				element: this
			});
		});
	})(window);
</script>

<!-- <div data-yjs-request="crud/largeTooltip" data-yjs-datasource-url="legals/tooltip/large" data-yjs-event-on="init" data-yjs-target="modal"></div> -->

<?php
if (isset($add_new_button)) {
	echo $this->Buttons->default('Add New', [
		'data' => [
			'yjs-request' => 'crud/load',
			'yjs-target' => 'modal',
			'yjs-datasource-url' => Router::url($add_new_button),
	    	'yjs-event-on' => "click"
	    ]
	]);
	echo '<br><br>';
}
?>

<?php if ($data->count()) : ?>
	<?= $this->element('AdvancedFilters.filter_objects'); ?>	
<?php else : ?>
	<?= $this->ContentPanels->render([
		'heading' => __('Nothing to show'),
		'body' => __('There is no filter to show here. Go back or create one in "Filters" dropdown menu.')
	]) ?>
<?php endif; ?>
