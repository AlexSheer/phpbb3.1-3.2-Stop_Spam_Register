<?php
/**
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopregister\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\sheer\stopregister\acp\main_module',
			'version'	=> '1.0.0',
			'title' => 'ACP_REGISTER_LOGS',
			'modes'		=> array(
				'settings'	=> array(
					'title' => 'ACP_REGISTER_LOGS',
					'auth' => 'ext_sheer/stopregister && acl_a_viewlogs',
					'cat' => array('ACP_FORUM_LOGS')
				),
			),
		);
	}
}
