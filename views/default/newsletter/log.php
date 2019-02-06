<?php
/**
 * Show the logging of the newsletter
 *
 * @uses $vars['entity'] The newsletter the view
 */

$entity = elgg_extract('entity', $vars);

$log = $entity->getLogging();
if (empty($log)) {
	echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:log:no_contents')]);
	return;
}

// general info
$table_data = '<tr>';
$table_data .= elgg_format_element('td', [], elgg_echo('newsletter:log:general:scheduled'));
$table_data .= elgg_format_element('td', [], elgg_view('output/date', [
	'value' => $entity->scheduled,
	'format' => elgg_echo('friendlytime:date_format'),
]));
$table_data .= '</tr>';
$table_data .= '<tr>';
$table_data .= elgg_format_element('td', [], elgg_echo('newsletter:log:general:starttime'));
$table_data .= elgg_format_element('td', [], elgg_view('output/date', [
	'value' => elgg_extract('start_time', $log),
	'format' => elgg_echo('friendlytime:date_format'),
]));
$table_data .= '</tr>';
$table_data .= '<tr>';
$table_data .= elgg_format_element('td', [], elgg_echo('newsletter:log:general:endtime'));
$table_data .= '<td>';
if ($entity->status == 'sent') {
	$table_data .= elgg_view('output/date', [
		'value' => elgg_extract('end_time', $log),
		'format' => elgg_echo('friendlytime:date_format'),
	]);
} else {
	$table_data .= elgg_echo('newsletter:status:' . $entity->status);
}
$table_data .= '</td>';
$table_data .= '</tr>';

$general = elgg_format_element('table', ['class' => 'elgg-table-alt'], $table_data);

echo elgg_view_module('info', elgg_echo('newsletter:log:general:title'), $general);

// recipient logging
$recipients = elgg_extract('recipients', $log);

if (empty($recipients)) {
	echo elgg_view('output/longtext', ['value' => elgg_echo('newsletter:log:no_recipients')]);
	return;
}

$users_title = elgg_echo('newsletter:log:users:title');
$emails_title = elgg_echo('newsletter:log:emails:title');

$users_header = '';
$users_content = '';
$users_row_class = '';
$users_counter = 0;
$users_error_counter = 0;
$emails_header = '';
$emails_content = '';
$emails_row_class = '';
$emails_counter = 0;
$emails_error_counter = 0;

foreach ($recipients as $recipient) {
	$type = elgg_extract('type', $recipient);
	
	if ($type == 'users') {
		$users_counter++;
		if (($users_counter > 25) && empty($users_row_class)) {
			$users_row_class = 'hidden';
		}
		$skip_columns = ['type', 'timestamp'];
		if (!elgg_is_admin_logged_in()) {
			$skip_columns[] = 'email';
		}
		
		if (empty($users_header)) {
			$keys = array_keys($recipient);
			
			$users_header = '';
			foreach ($keys as $key) {
				if (in_array($key, $skip_columns)) {
					continue;
				}
				
				$options = [];
				if ($key == 'status') {
					$options['class'] = 'center';
				}
				$users_header .= elgg_format_element('th', $options, elgg_echo("newsletter:log:users:header:{$key}"));
			}
			$users_header = elgg_format_element('tr', [], $users_header);
		}
		
		$users_content_data = '';
		foreach ($recipient as $key => $data) {
			if (in_array($key, $skip_columns)) {
				continue;
			}
			
			switch ($key) {
				case 'guid':
					$user = get_user($data);
					$users_content_data .= elgg_format_element('td', [], $user->name);
					break;
				case 'status':
					if ($data) {
						$users_content_data .= elgg_format_element('td', ['class' => 'center'], elgg_view_icon('checkmark'));
					} else {
						$users_error_counter++;
						$users_content_data .= elgg_format_element('td', ['class' => 'center'], elgg_view_icon('attention'));
					}
					break;
				default:
					$users_content_data .= elgg_format_element('td', [], $data);
					break;
			}
		}
		$users_content .= elgg_format_element('tr', ['class' => $users_row_class], $users_content_data);
	} elseif ($type == 'emails') {
		$emails_counter++;
		if (($emails_counter > 25) && empty($emails_row_class)) {
			$emails_row_class = 'hidden';
		}
		$skip_columns = ['type', 'timestamp'];
		
		if (empty($emails_header)) {
			$keys = array_keys($recipient);
				
			$emails_header = '';
			foreach ($keys as $key) {
				if (in_array($key, $skip_columns)) {
					continue;
				}
				
				$options = [];
				if ($key == 'status') {
					$options['class'] = 'center';
				}
				
				$emails_header .= elgg_format_element('th', $options, elgg_echo("newsletter:log:email:header:{$key}"));
			}
			$emails_header = elgg_format_element('tr', [], $emails_header);
		}
		
		$emails_content_data = '';
		foreach ($recipient as $key => $data) {
			if (in_array($key, $skip_columns)) {
				continue;
			}
			
			switch ($key) {
				case 'status':
					if ($data) {
						$emails_content_data .= elgg_format_element('td', ['class' => 'center'], elgg_view_icon('checkmark'));
					} else {
						$emails_error_counter++;
						$emails_content_data .= elgg_format_element('td', ['class' => 'center'], elgg_view_icon('attention'));
					}
					break;
				default:
					$emails_content_data .= elgg_format_element('td', [], $data);
					break;
			}
		}
		$emails_content .= elgg_format_element('tr', ['class' => $emails_row_class], $emails_content_data);
	}
}

if (!empty($users_content)) {
	$users_title .= ' (';
	$users_title .= ($users_counter - $users_error_counter) . ' ' . elgg_echo('newsletter:log:counter:success');
	$users_title .= ', ' . $users_error_counter . ' ' . elgg_echo('newsletter:log:counter:error');
	$users_title .= ')';
	
	$users_content = elgg_format_element('table', [
		'class' => 'elgg-table',
		'id' => 'newsletter-log-users-table',
	], $users_header . $users_content);
	
	if (!empty($users_row_class)) {
		$more_link = elgg_view('output/url', [
			'text' => elgg_echo('more'),
			'href' => '#newsletter-log-users-table',
			'onclick' => '$("#newsletter-log-users-table tr.hidden").show();$(this).parent().remove();',
			'class' => 'float-alt',
		]);
		$users_content .= elgg_format_element('div', ['class' => 'mtm'], $more_link);
	}
} else {
	$users_content = elgg_view('output/longtext', ['value' => elgg_echo('newsletter:log:users:no_recipients')]);
}

if (!empty($emails_content)) {
	$emails_title .= ' (';
	$emails_title .= ($emails_counter - $emails_error_counter) . ' ' . elgg_echo('newsletter:log:counter:success');
	$emails_title .= ', ' . $emails_error_counter . ' ' . elgg_echo('newsletter:log:counter:error');
	$emails_title .= ')';
	
	$emails_content = elgg_format_element('table', [
		'class' => 'elgg-table',
		'id' => 'newsletter-log-emails-table',
	], $emails_header . $emails_content);
	
	if (!empty($emails_row_class)) {
		$more_link = elgg_view('output/url', [
			'text' => elgg_echo('more'),
			'href' => '#newsletter-log-emails-table',
			'onclick' => '$("#newsletter-log-emails-table tr.hidden").show();$(this).parent().remove();',
			'class' => 'float-alt',
		]);
		$emails_content .= elgg_format_element('div', ['class' => 'mtm'], $more_link);
	}
} else {
	$emails_content = elgg_view('output/longtext', ['value' => elgg_echo('newsletter:log:emails:no_recipients')]);
}

echo elgg_view_module('info', $users_title, $users_content);
echo elgg_view_module('info', $emails_title, $emails_content);
