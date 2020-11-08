<?php
// ddd($data);
echo $this->Html->script("LimitlessTheme.plugins/ui/moment/moment.min");
echo $this->Html->script("LimitlessTheme.plugins/ui/fullcalendar/fullcalendar.min");
?>
<?php
// add toolbar buttons for dashboard pages
$this->Dashboard->addToolbarBtns();
?>

<div class="text-right">
	<?php if (!empty($calendarLastSyncDate)) : ?>
		<p><?= __('The calendar was last calculated at %s and will be calculated again in %s minutes.', date('jS M g:ma', strtotime($calendarLastSyncDate)), 60 - date('i')); ?></p>
	<?php else : ?>
		<p><?= __('The calendar will be calculated in %s minutes.', 60 - date('i')); ?></p>
	<?php endif; ?>
</div>

<div class="panel panel-flat">
	<div class="panel-body">
		<div id="calendar"></div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){

	//===== Calendar =====//
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	var h = {};

	if ($('#calendar').width() <= 400) {
		h = {
			left: 'title',
			center: '',
			right: 'prev,next'
		};
	} else {
		h = {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		};
	}

	$('#calendar').fullCalendar({
		disableDragging: false,
		header: h,
		titleFormat: 'MMMM',
		eventClick: function(event) {
		    if (event.url) {
		      window.open(event.url);
		      return false;
		    }
		},
		eventAfterRender: function(eventObj, $el) {
			if ($el.find(".fc-content").width() < $el.find(".fc-title").width()) {
				$el.popover({
					content: eventObj.title,
					// title: 'Event',
					template: '<div class="popover">' +
								'<div class="arrow"></div>' +
								// '<h3 class="popover-title"></h3>' + 
								'<div class="popover-content"></div>' + 
								'</div>',
					trigger: 'hover',
					placement: 'top',
					container: 'body'
				});
			}
		},
		validRange: function(nowDate) {
			return {
			  start: nowDate.clone().subtract(2, 'months'),
			  end: nowDate.clone().add(2, 'months')
			};
		},
		eventLimit: true,
		editable: false,
		events: <?= json_encode($data); ?>
	});

	});
</script>