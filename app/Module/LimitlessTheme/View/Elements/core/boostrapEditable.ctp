<?php
echo $this->Html->script('plugins/bootstrap-editable/bootstrap-editable', array('inline' => true));
echo $this->Html->css('plugins/bootstrap-editable', null, array('inline' => true));
echo $this->Html->scriptBlock("
	$(document).ready(function() {
		//inline edit
		$.fn.editable.defaults.mode = 'inline';
		//json return
		$.fn.editable.defaults.ajaxOptions = {dataType: 'json'};
		//po spracovani
		$.fn.editable.defaults.success =  function(response, newValue) {
	        //console.log(response);
	        if(!response.success){
	        	return response.msg;
	        }

	        if(response.newValue != ''){
	        	return {newValue: response.newValue};
	        }
	    };

	    $.fn.editable.defaults.emptytext = '-';
	});
");
?>