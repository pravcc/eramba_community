<?php 
$cacheOptions = array();
$cacheOptions['engine'] = 'File';

Configure::write('cacheOptions', $cacheOptions);

Cache::config('default', am(
	array(
		'duration'=> '+5 minutes',  //+5 minutes
		'prefix' => 'cake_default_'
	), 
	$cacheOptions
));

Cache::config('short', am(
	array(
		'duration'=> '+10 minutes', //+10 minutes
		'prefix' => 'cake_short_'
	), 
	$cacheOptions
));

Cache::config('medium', am(
	array(
		'duration'=> '+4 hours', //+4 hours
		'prefix' => 'cake_medium_'
	), 
	$cacheOptions
));

Cache::config('hourly', am(
	array(
		'duration'=> '+1 hour', //+1 hour
		'prefix' => 'cake_hourly_'
	), 
	$cacheOptions
));

Cache::config('long', am(
	array(
		'duration'=> '+1 day', //+1 day
		'prefix' => 'cake_long_'
	), 
	$cacheOptions
));

Cache::config('menu', am(
	array(
		'duration'=> '+1 day', //+1 day
		'prefix' => 'cake_menu_',
		'groups' => array('articles', 'pages', 'blogs')
	), 
	$cacheOptions
));

Cache::config('infinite', am(
	array(
		'duration'=> '+1 year', //+1 year
		'prefix' => 'cake_infinite_'
	), 
	$cacheOptions
));

Cache::config('authErrors', am(
	array(
		'duration'=> '+1 month', //+1 month
		'prefix' => 'cake_autherr_',
		'groups' => array('users')
	), 
	$cacheOptions
));

Cache::config('acl', am(
	array(
		'duration'=> '+6 months', //+1 month
		'prefix' => 'cake_acl_',
		'groups' => array('acl')
	), 
	$cacheOptions
));

Cache::config('updates', am(
	array(
		'duration'=> '+1 day',
		'prefix' => 'cake_updates_',
		'groups' => array('updates')
	), 
	$cacheOptions
));

Cache::config('news', am(
	array(
		'duration'=> '+1 day',
		'prefix' => 'cake_news_',
		'groups' => array('news')
	), 
	$cacheOptions
));

Cache::config('ldap', am(
	array(
		'duration'=> '+5 hours',
		'prefix' => 'cake_ldap_',
		'groups' => array('ldap')
	), 
	$cacheOptions
));

Cache::config('cron', am(
	array(
		'duration'=> '+1 day',
		'prefix' => 'cake_cron_',
		'groups' => array('cron')
	), 
	$cacheOptions
));

Cache::config('field_data', am(
	array(
		'duration'=> '+1 day',
		'prefix' => 'cake_field_data_',
		'groups' => array('field_data')
	), 
	$cacheOptions
));

Cache::config('widget_data', am(
	[
		'duration'=> '+1 week',
		'prefix' => 'widget_data_',
		'groups' => ['widget']
	], 
	$cacheOptions
));

Cache::config('widget_settings', am(
	[
		'duration' => '+1 day',
		'prefix' => 'widget_settings_',
		'groups' => ['widget']
	], 
	$cacheOptions
));

Cache::config('app_notification', am(
	[
		'duration' => '+1 day',
		'prefix' => 'app_notification_',
		'groups' => ['app_notification']
	], 
	$cacheOptions
));

Cache::config('reports_settings', am(
	[
		'duration'=> '+1 day',
		'prefix' => 'reports_settings_',
		'groups' => ['reports']
	], 
	$cacheOptions
));

Cache::config('notification_system_settings', am(
	[
		'duration'=> '+1 day',
		'prefix' => 'notification_system_settings_',
		'groups' => ['notification_system']
	], 
	$cacheOptions
));

Cache::config('advanced_filters_settings', am(
	[
		'duration'=> '+1 day',
		'prefix' => 'advanced_filters_settings_',
		'groups' => ['advanced_filters']
	], 
	$cacheOptions
));

Cache::config('trash_settings', am(
	[
		'duration'=> '+1 day',
		'prefix' => 'trash_settings_',
		'groups' => ['trash']
	], 
	$cacheOptions
));

Cache::config('custom_fields_settings', am(
	[
		'duration'=> '+1 day',
		'prefix' => 'custom_fields_settings_',
		'groups' => ['custom_fields']
	], 
	$cacheOptions
));

Cache::config('layout_toolbar', am(
	[
		'duration'=> '+1 day',
		'prefix' => 'layout_toolbar_',
		'groups' => ['layout_toolbar']
	], 
	$cacheOptions
));

Cache::config('custom_labels', am(
	[
		'duration'=> '+1 week',
		'prefix' => 'custom_labels_',
		'groups' => ['custom_labels']
	], 
	$cacheOptions
));

Cache::config('settings', am(
	[
		'duration' => '+10 minutes',
		'prefix' => 'settings_',
		'groups' => ['settings']
	], 
	$cacheOptions
));


/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
	Cache::config('_cake_model_', array('duration' => '+10 seconds'));
	Cache::config('_cake_core_', array('duration' => '+10 seconds'));
}