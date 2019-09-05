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
	'ACP_STOPFORUMSPAM'			=> 'Проверка по БД www.stopforumspam.com',
	'IP_STOP'					=> 'Ваш IP-адрес внесен в черный список сервиса <a href="https://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a>',
	'NICK_STOP'					=> 'Выбранное вами имя внесено в черный список сервиса <a href="http://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a><br />Попробуйте подобрать другое',
	'EMAIL_STOP'				=> 'Указанный адрес e-mail внесен в черный список сервиса <a href="https://www.stopforumspam.com" target = "_blank">www.stopforumspam.com</a><br />Попробуйте использовать другой',
	'REPORT_STOP'				=> 'Если вы считаете, что это произошло по недоразумению,  отправьте <a href="https://www.stopforumspam.com/removal" target = "_blank">запрос на удаление данных из базы данных stopforumspam.com</a>',
	'ALLOW_STOPFORUMSPAM'		=> 'Включить',
	'ALLOW_STOPFORUMSPAM_EXPLAIN'=> 'Если включено, то IP-адрес, имя пользователя и адрес e-mail будут проверены по базе данных www.stopforumspam.com<br />Отключение <b>всех трех</b> нижеследующих параметров равносильно отключению этого параметра.',
	'ALLOW_REG_LOG'				=> 'Вести лог регистраций',
	'CHECK_SPAM_IP'				=> 'Проверять по IP-адресу',
	'CHECK_USERNAME'			=> 'Проверять имя пользователя',
	'CHECK_SPAM_EMAIL'			=> 'Проверять адрес e-mail',
	'SFSL_PRUNE_DAY'=> 'Автоочистка лога',
	'SFSL_PRUNE_DAY_EXPLAIN'=> 'За какое время в сутках хранить данные. Нулевое значение отключает автоматическую очистку лога.',
));
