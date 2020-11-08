/**
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 */
function init_register_user_controller_toggle_right(app_root_url, user_id, plugin, controller, missing_aco_text)
{
	app_root_url     = ensure_ends_with(app_root_url, "/");
	var plugin_param = (plugin == null || plugin == "" || typeof(plugin) == "undefined") ? '' : "/plugin:" + plugin;
	
	var url = app_root_url + "admin/acl/aros/get_user_controller_permission/" + user_id + plugin_param + "/controller:" + controller;

	$.ajax({	url: url,
				dataType: "html", 
				cache: false,
				success: function (data, textStatus) 
				{
					//alert(data);
					permissions = jQuery.parseJSON(data);
					//alert(permissions);
					
					for(var action in permissions)
					{
						var start_granted = false;
						var span_id       = "right_" + plugin + "_" + user_id + "_" + controller + "_" + action; 
						
						if(permissions[action] == true || permissions[action] == false)
						{
							if(permissions[action] == true)
							{
								//icon_html = "<img src=\"" + app_root_url + "acl/img/design/tick.png"  + "\" class=\"pointer\" alt=\"granted\" />";
								icon_html = '<i class="icon-checkmark5 icon-green icon-2x pointer"></i>';
								start_granted = true;
							}
							else
							{
								//icon_html = "<img src=\"" + app_root_url + "acl/img/design/cross.png"  + "\" class=\"pointer\" alt=\"denied\" />";
								icon_html = '<i class="icon-cross icon-red icon-2x pointer"></i>';
								start_granted = false;
							}
							
							$("#" + span_id).html(icon_html);
							
							register_user_toggle_right(start_granted, app_root_url, span_id, user_id, plugin, controller, action);
						}
						else
						{
							//icon_html = "<img src=\"" + app_root_url + "acl/img/design/important16.png"  + "\" alt=\"" + missing_aco_text + "\" title=\"" + missing_aco_text + "\" />";
							icon_html = '<i class="icon-exclamation icon-orange icon-2x pointer" title="' + missing_aco_text + '"></i>';
							
							$("#" + span_id).html(icon_html);
						}
					}
				}
			});
}
function register_user_toggle_right(start_granted, app_root_url, span_id, user_id, plugin, controller, action)
{
	app_root_url     = ensure_ends_with(app_root_url, "/");
	var plugin_param = (plugin == null || plugin == "" || typeof(plugin) == "undefined") ? '' : "/plugin:" + plugin;
	
	if(start_granted)
	{
		var url1 = app_root_url + "admin/acl/aros/deny_user_permission/" + user_id + plugin_param + "/controller:" + controller + "/action:" + action;
		var url2 = app_root_url + "admin/acl/aros/grant_user_permission/" + user_id + plugin_param + "/controller:" + controller + "/action:" + action;
	}
	else
	{
		var url1 = app_root_url + "admin/acl/aros/grant_user_permission/" + user_id + plugin_param + "/controller:" + controller + "/action:" + action;
		var url2 = app_root_url + "admin/acl/aros/deny_user_permission/" + user_id + plugin_param + "/controller:" + controller + "/action:" + action;
	}
	
	$("#" + span_id).toggle(function()
                    		{
								$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action + "_spinner").show();
								
								$.ajax({	url: url1,
											dataType: "html", 
											cache: false,
    										success: function (data, textStatus) 
    										{
    											$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action).html(data);
    										},
    										complete: function()
        									{
        										$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action + "_spinner").hide();
        									}
										});
                    		}, 
                    		function()
                    		{
                    			$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action + "_spinner").show();
                    			
                    			$.ajax({	url: url2,
        									dataType: "html", 
        									cache: false,
        									success: function (data, textStatus) 
        									{
        										$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action).html(data);
        									},
        									complete: function()
        									{
        										$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action + "_spinner").hide();
        									}
								});
                    		}); 
}


function init_register_role_controller_toggle_right(app_root_url, role_id, plugin, controller, missing_aco_text)
{
	app_root_url     = ensure_ends_with(app_root_url, "/");
	var plugin_param = (plugin == null || plugin == "" || typeof(plugin) == "undefined") ? '' : "/plugin:" + plugin;
	
	var url = app_root_url + "admin/acl/aros/get_role_controller_permission/" + role_id + plugin_param + "/controller:" + controller;

	$.ajax({	url: url,
				dataType: "html", 
				cache: false,
				success: function (data, textStatus) 
				{
					//console.log(data);
					permissions = jQuery.parseJSON(data);
					//alert(permissions);
					
					for(var action in permissions)
					{
						var start_granted = false;
						var span_id       = "right_" + plugin + "_" + role_id + "_" + controller + "_" + action; 
						
						if(permissions[action] == true || permissions[action] == false)
						{
							if(permissions[action] == true)
							{
								//icon_html = "<img src=\"" + app_root_url + "acl/img/design/tick.png"  + "\" class=\"pointer\" alt=\"granted\" />";
								icon_html = '<i class="icon-checkmark5 icon-green icon-2x pointer"></i>';
								start_granted = true;
							}
							else
							{
								//icon_html = "<img src=\"" + app_root_url + "acl/img/design/cross.png"  + "\" class=\"pointer\" alt=\"denied\" />";
								icon_html = '<i class="icon-cross icon-red icon-2x pointer"></i>';
								start_granted = false;
							}
							
							$("#" + span_id).html(icon_html);
							//console.log(start_granted);
							register_role_toggle_right(start_granted, app_root_url, span_id, role_id, plugin, controller, action);
						}
						else
						{
							//icon_html = "<img src=\"" + app_root_url + "acl/img/design/important16.png"  + "\" alt=\"" + missing_aco_text + "\" title=\"" + missing_aco_text + "\" />";
							icon_html = '<i class="icon-exclamation icon-orange icon-2x pointer" title="' + missing_aco_text + '"></i>';
							
							$("#" + span_id).html(icon_html);
						}
					}
				}
			});
}

function register_role_toggle_right(start_granted, app_root_url, span_id, role_id, plugin, controller, action)
{
	app_root_url     = ensure_ends_with(app_root_url, "/");
	var plugin_param = (plugin == null || plugin == "" || typeof(plugin) == "undefined") ? '' : "/plugin:" + plugin;
	
	if(start_granted)
	{
		var url1 = app_root_url + "admin/acl/aros/deny_role_permission/" + role_id + plugin_param + "/controller:" + controller + "/action:" + action;
		var url2 = app_root_url + "admin/acl/aros/grant_role_permission/" + role_id + plugin_param + "/controller:" + controller + "/action:" + action;
	}
	else
	{
		var url1 = app_root_url + "admin/acl/aros/grant_role_permission/" + role_id + plugin_param + "/controller:" + controller + "/action:" + action;
		var url2 = app_root_url + "admin/acl/aros/deny_role_permission/" + role_id + plugin_param + "/controller:" + controller + "/action:" + action;
	}

	var url1 = app_root_url + "admin/acl/aros/grant_role_permission/" + role_id + plugin_param + "/controller:" + controller + "/action:" + action;
	var url2 = app_root_url + "admin/acl/aros/deny_role_permission/" + role_id + plugin_param + "/controller:" + controller + "/action:" + action;

	$("#" + span_id).data('start_granted', start_granted);
	
	var url;
	$("#" + span_id).on("click", function(e) {
		var start_granted = $(this).data('start_granted');
		if ( $(this).data('start_granted') ) {
			url = url2;
		} else {
			url = url1;
		}


		$(this).data('start_granted', !start_granted);

		$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").show();
		$.ajax({
			url: url,
			dataType: "html", 
			cache: false,
			success: function (data, textStatus) 
			{
				$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action).html(data);
			},
			complete: function()
			{
				$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").hide();
			}
		});
	});
	/*$("#" + span_id).toggle(function()
		{
			$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").show();
			
			$.ajax({	url: url1,
						dataType: "html", 
						cache: false,
						success: function (data, textStatus) 
						{
							$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action).html(data);
						},
						complete: function()
						{
							$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").hide();
						}
					});
		}, 
		function()
		{
			$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").show();
			
			$.ajax({	url: url2,
						dataType: "html", 
						cache: false,
						success: function (data, textStatus) 
						{
							$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action).html(data);
						},
						complete: function()
						{
							$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").hide();
						}
			});
		});*/
}

function ensure_ends_with(text, trailing_text)
{
	if(text.length < trailing_text.length || text.substring(text.length - trailing_text.length) != trailing_text)
	{
		text = text + trailing_text;
	}
	
	return text;
}
