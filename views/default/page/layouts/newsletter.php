<?php

/**
 * A layout for the newsletter, to be used in preview, online and mail
 *
 * @uses $vars['entity'] 	The newsletter to be viewed
 */

$language = get_current_language();

$entity = $vars["entity"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<base target="_blank" />
		
		<title><?php echo $entity->title;?></title>
	</head>
	<body>
		<style type="text/css">
			<?php echo elgg_view("newsletter/view/css", $vars); ?>
		</style>
		<?php echo elgg_view("newsletter/view/body", $vars); ?>
	</body>
</html>