<?php
/**
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2018 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace sheer\stopregister\migrations;

class stopregister_1_0_4 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['stopregister_version']) && version_compare($this->config['stopregister_version'], '1.0.4', '>=');
	}

	static public function depends_on()
	{
		return array('\sheer\stopregister\migrations\stopregister_1_0_3');
	}

	public function update_schema()
	{
		return array(
		);
	}
	public function revert_schema()
	{
		return array(
		);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.update', array('stopregister_version', '1.0.4')),
		);
	}
}