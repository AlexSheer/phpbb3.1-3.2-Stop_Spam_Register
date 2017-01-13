<?php
/**
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace sheer\stopregister\migrations;

class stopregister_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return;
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'register_log'	=> array(
					'COLUMNS'		=> array(
						'log_id'			=> array('UINT', null, 'auto_increment'),
						'log_type'			=> array('TINT:4', 0),
						'user_id'			=> array('UINT', 0),
						'forum_id'			=> array('UINT', 0),
						'reportee_id'		=> array('UINT', 0),
						'topic_id'			=> array('UINT', 0),
						'post_id'			=> array('UINT', 0),
						'log_ip'			=> array('VCHAR:40', ''),
						'log_time'			=> array('UINT:11', 0),
						'log_operation'		=> array('TEXT', ''),
						'log_data'			=> array('MTEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'log_id',
						'KEYS'			=> array(
							'log_type'		=> array('INDEX', 'log_type'),
							'user_id'		=> array('INDEX', 'user_id'),
						),
				),
			),
		);
	}
	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'register_log',
			),
		);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('stopregister_version', '1.0.0')),
			array('config.add', array('enable_stopforumspam', '0', '0')),
			array('config.add', array('check_username', '0', '0')),
			array('config.add', array('check_email', '0', '0')),
			array('config.add', array('check_ip', '0', '0')),
			array('config.add', array('enable_register_log', '0', '0')),
			// ACP
			array('module.add', array('acp', 'ACP_FORUM_LOGS', array(
				'module_basename'	=> '\sheer\stopregister\acp\main_module',
				'module_langname'	=> 'ACP_REGISTER_LOGS',
				'module_mode'		=> 'register',
				'module_auth'		=> 'ext_sheer/stopregister && acl_a_viewlogs',
				'module_enabled'	=> true,
			))),
		);
	}
}