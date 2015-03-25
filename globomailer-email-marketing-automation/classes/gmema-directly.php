<?php
function gmema_plugin_query_vars($vars) 
{
	$vars[] = 'gmema';
	return $vars;
}
add_filter('query_vars', 'gmema_plugin_query_vars');

function gmema_plugin_parse_request($qstring)
{
	if (array_key_exists('gmema', $qstring->query_vars)) 
	{
		$page = $qstring->query_vars['gmema'];
		switch($page)
		{
			case 'subscribe':
				require_once(GMEMA_DIR.'job'.DIRECTORY_SEPARATOR.'gmema-subscribe.php');
				break;
			case 'unsubscribe':
				require_once(GMEMA_DIR.'job'.DIRECTORY_SEPARATOR.'gmema-unsubscribe.php');
				break;
		}
	}
}
add_action('parse_request', 'gmema_plugin_parse_request');
?>