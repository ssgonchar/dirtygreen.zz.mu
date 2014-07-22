<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     escape string for not own link
 * -------------------------------------------------------------
 */
function smarty_modifier_html_if_own($string, $status_id = LINK_STATUS_TEMP)
{
	if ($status_id == LINK_STATUS_OWN)
		return $string;
	else
		return htmlspecialchars($string);
}

?>
