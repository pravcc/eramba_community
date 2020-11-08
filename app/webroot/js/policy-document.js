jQuery(function($) {
	attachEvents($("[data-document-link]"));

	$("#content").on("Eramba.reloadIndex", function(e) {
		attachEvents($("[data-document-link]"));
	});

	function attachEvents(ele) {
		$(ele).off('click.Eramba').on('click.Eramba', function(e){
			var title = $(this).data("doc-title");
			var href = $(this).attr("href");

			$.ajax({
				type: "GET",
				dataType: "html",
				url: href
			}).done(function(data) {
				var html = $(data);
				attachEvents(html.find('[data-document-link]'));

				bootbox.hideAll();
				bootbox.dialog({
					//title: title,
					message : html,
					className: "document-modal",
					/*buttons: {
						close: {
							label: "<?php echo __('Close'); ?>",
							className: "btn",
						}
					}*/
				});
			});
			
			e.preventDefault();
		});
	}
	
});