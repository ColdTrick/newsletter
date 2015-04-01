<?php
/**
 * Pagehandlers for newsletter
 *
 */

/**
 * Handles pages for newsletter
 *
 * @param array $page requested page
 *
 * @return void
 */
function newsletter_page_handler($page) {
	$include_file = false;
	$path = dirname(dirname(__FILE__));
	
	switch ($page[0]) {
		case "edit":
			set_input("guid", elgg_extract("1", $page));
			set_input("subpage", elgg_extract("2", $page));
			$include_file = $path . "/pages/edit.php";
			break;
		case "preview":
			set_input("guid", elgg_extract("1", $page));
			$include_file = $path . "/pages/preview.php";
			break;
		case "embed":
			set_input("guid", elgg_extract("1", $page));
			$include_file = $path . "/pages/embed.php";
			break;
		case "add":
			if (isset($page[1]) && is_numeric($page[1])) {
				elgg_set_page_owner_guid($page[1]);
				
				$include_file = $path . "/pages/add.php";
			}
			break;
		case "view":
			if (isset($page[1]) && is_numeric($page[1])) {
				set_input("guid", $page[1]);
				
				if (!empty($page[2])) {
					set_input("code", $page[2]);
				}
				
				$include_file = $path . "/pages/view.php";
			}
			break;
		case "group":
			if (isset($page[1]) && is_numeric($page[1])) {
				elgg_set_page_owner_guid($page[1]);
				
				$include_file = $path . "/pages/group.php";
			}
			break;
		case "site":
			$include_file = $path . "/pages/site.php";
			break;
		case "unsubscribe":
			if (isset($page[1]) && is_numeric($page[1])) {
				set_input("guid", (int)$page[1]);
				
				$include_file = $path . "/pages/unsubscribe.php";
			}
			break;
		case "recipients":
			$include_file = $path . "/procedures/recipients.php";
			break;
		case "subscriptions":
			if (isset($page[1]) && ($user = get_user_by_username($page[1]))) {
				elgg_set_page_owner_guid($user->getGUID());
					
				$include_file = $path . "/pages/subscriptions.php";
			}
			break;
		case "received":
			if (isset($page[1]) && ($user = get_user_by_username($page[1]))) {
				elgg_set_page_owner_guid($user->getGUID());
					
				$include_file = $path . "/pages/received.php";
			}
			break;
		case "log":
			if (isset($page[1]) && is_numeric($page[1])) {
				set_input("guid", $page[1]);
					
				$include_file = $path . "/pages/log.php";
			}
			break;
		default:
			forward("newsletter/site");
			break;
	}
	
	if (!empty($include_file)) {
		include($include_file);
		return true;
	}
	
	return false;
}
