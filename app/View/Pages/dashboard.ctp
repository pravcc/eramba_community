<?php
echo $this->Html->script("LimitlessTheme.plugins/ui/moment/moment.min");
echo $this->Html->script("LimitlessTheme.plugins/ui/fullcalendar/fullcalendar.min");
?>
<?php
// add toolbar buttons for dashboard pages
$this->Dashboard->addToolbarBtns();
?>

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
		eventLimit: true,
		editable: false,
		events: <?php echo json_encode( $calendar_events ); ?>
	});

	});
</script>