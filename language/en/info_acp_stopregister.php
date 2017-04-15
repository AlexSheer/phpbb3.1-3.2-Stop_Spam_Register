<?php
/**
*
* info_acp_stopregister [English]
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_REGISTER_LOGS'			=> 'Registrations Logs',
 	'ACP_REGISTER_LOGS_EXPLAIN'	=> 'This is a list of attempts to register users.',
	'IP_BLACKLIST'				=> '<span style="color: #BC2A4D;">IP-address was blacklised in <a href="http://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a>',
	'NICK_BLACKLIST'			=> 'Username <strong style="color: #BC2A4D;">%1$s</span> was blacklised in <a href="http://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a>',
	'EMAIL_BLACKLIST'			=> 'E-mail <strong style="color: #BC2A4D;">%1$s</strong> was blacklised in <a href="http://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a>',
	'REGISTER_SUCSESS'			=> 'User <strong>%1$s</strong> was successfully registered',
	'EXCEED_REGISTERS'			=> '<strong style="color: #BC2A4D;">The number of registration attempts was exceeded. Process terminated</strong>',
	'LOG_CLEAR_REGISTER_LOG'	=> '<strong>Registrations Logs cleared</strong>',
	'ACP_LOGS_SORT'				=> 'Filter',
	'ACP_LOGS_ALL'				=> 'All',
));
