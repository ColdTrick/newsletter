<?php

	function newsletter_page_handler($page) {
		$include_file = false;
		
		switch ($page[0]) {
			case "edit":
				if (isset($page[1]) && is_numeric($page[1])) {
					set_input("guid", $page[1]);
					
					$subpage = elgg_extract("2", $page);
					switch ($subpage) {
						case "content":
							$include_file = dirname(dirname(__FILE__)) . "/pages/edit/content.php";
							break;
						case "recipients":
							$include_file = dirname(dirname(__FILE__)) . "/pages/edit/recipients.php";
							break;
						case "schedule":
							$include_file = dirname(dirname(__FILE__)) . "/pages/edit/schedule.php";
							break;
						case "styling":
							$include_file = dirname(dirname(__FILE__)) . "/pages/edit/styling.php";
							break;
						default:
							$include_file = dirname(dirname(__FILE__)) . "/pages/edit/newsletter.php";
							break;
					}
				}
				break;
			case "add":
				if (isset($page[1]) && is_numeric($page[1])) {
					elgg_set_page_owner_guid($page[1]);
					
					$include_file = dirname(dirname(__FILE__)) . "/pages/add.php";
				}
				break;
			case "view":
				if (isset($page[1]) && is_numeric($page[1])) {
					set_input("guid", $page[1]);
					
					$include_file = dirname(dirname(__FILE__)) . "/pages/view.php";
				}
				break;
			case "group":
				if (isset($page[1]) && is_numeric($page[1])) {
					elgg_set_page_owner_guid($page[1]);
					
					$include_file = dirname(dirname(__FILE__)) . "/pages/group.php";
				}
				break;
			case "site":
				$include_file = dirname(dirname(__FILE__)) . "/pages/site.php";
				break;
			case "subscribe":
				if (isset($page[1]) && is_numeric($page[1])) {
					elgg_set_page_owner_guid($page[1]);
					
					$include_file = dirname(dirname(__FILE__)) . "/pages/subscribe.php";
				}
				break;
			case "unsubscribe":
				if (isset($page[1]) && is_numeric($page[1])) {
					elgg_set_page_owner_guid($page[1]);
					
					$include_file = dirname(dirname(__FILE__)) . "/pages/unsubscribe.php";
				}
				break;
			case "recipients":
				$include_file = dirname(dirname(__FILE__)) . "/procedures/recipients.php";
				break;
			case "subscriptions":
				if (isset($page[1]) && is_numeric($page[1])) {
					elgg_set_page_owner_guid($page[1]);
						
					$include_file = dirname(dirname(__FILE__)) . "/pages/subscriptions.php";
				}
				break;
			default:
				forward("newsletter/site");
				break;
		}
		
		if ($include_file) {
			include($include_file);
			return true;
		}
		
		return false;
	}