<?php
/**
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2017 Sheer
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_STOPFORUMSPAM'			=> 'Checking from database www.stopforumspam.com',
	'IP_STOP'					=> 'Your IP address is blacklisted in <a href="https://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a>',
	'NICK_STOP'					=> 'Username is blacklisted ih <a href="https://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a><br />Попробуйте подобрать другое',
	'EMAIL_STOP'				=> 'E-mail v <a href="http://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a><br />Попробуйте использовать другой',
	'REPORT_STOP'				=> 'If you believe that this was a misunderstanding, send a <a href="https://www.stopforumspam.com/removal" target = "_blank">request to delete data from the stopforumspam.com database</a> ',
	'ALLOW_STOPFORUMSPAM'		=> 'Enable',
	'ALLOW_STOPFORUMSPAM_EXPLAIN'=> 'If enabled, the IP address, username and e-mail address will be checked from database www.stopforumspam.com<br />Disabling <b>all three</b> of the following options is equivalent to disabling this setting.',
	'ALLOW_REG_LOG'				=> 'Enable logs',
	'CHECK_SPAM_IP'				=> 'Check by IP address',
	'CHECK_USERNAME'			=> 'Check by username',
	'CHECK_SPAM_EMAIL'			=> 'Check by e-mail',
));
