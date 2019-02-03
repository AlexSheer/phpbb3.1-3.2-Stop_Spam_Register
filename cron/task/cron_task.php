<?php
/**
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2019 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace sheer\stopregister\cron\task;

class cron_task extends \phpbb\cron\task\base
{
	/**
	 * How often we run the cron (in seconds).
	 * @var int
	 */
	protected $cron_frequency = 86400; //24 hours

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log_interface */
	protected $log;

	/** @var string REGISTER_LOG_TABLE */
	protected $sfs_log_table;

	/**
	* Constructor
	*/
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\user $user,
		\phpbb\log\log $phpbb_log,
		$sfs_log_table
		)
	{
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->phpbb_log = $phpbb_log;
		$this->sfs_log = $sfs_log_table;
	}

	/**
	* Runs this cron task.
	*
	* @return null
	*/
	public function run()
	{
		if ($this->config['sfsl_expire_days'])
		{
			$diff = time() - ($this->config['sfsl_expire_days'] * 86400);
			$sql = 'DELETE FROM ' . $this->sfs_log . ' WHERE log_time < ' . $diff . '';
			$this->db->sql_query($sql);
			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->data['session_ip'], 'LOG_CLEAR_REGISTER_LOG', time(), false);

			// Update the cron task run time here if it hasn't
			// already been done by cron actions.
			$this->config->set('stopreglog_last_run', time(), true);
		}
	}

	/**
	* Returns whether this cron task can run, given current board configuration.
	*
	* For example, a cron task that prunes forums can only run when
	* forum pruning is enabled.
	*
	* @return bool
	*/
	public function is_runnable()
	{
		if ($this->config['enable_register_log'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	* Returns whether this cron task should run now, because enough time
	* has passed since it was last run.
	*
	* @return bool
	*/
	public function should_run()
	{
		return $this->config['stopreglog_last_run'] < time() - $this->cron_frequency;
	}
}
