<?php
/**
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace sheer\stopregister\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
/**
* Assign functions defined in this class to event listeners in the core
*
* @return array
* @static
* @access public
*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'					=> 'load_language_on_setup',
			'core.ucp_register_data_after'		=> 'chk_user_data',
			'core.acp_board_config_edit_add'	=> 'add_acp_config',
			'core.get_logs_modify_type'			=> 'add_sql_where',
			'core.ucp_register_user_row_after'	=> 'add_log_register',
			'core.posting_modify_submit_post_before' => 'posting_before'
		);
	}

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log_interface */
	protected $log;

/**
* Constructor
*/
	public function __construct(
		\phpbb\request\request_interface $request,
		\phpbb\template\template $template,
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\log\log_interface $log,
		$table_prefix
	)
	{
		$this->request = $request;
		$this->template = $template;
		$this->config = $config;
		$this->user = $user;
		$this->phpbb_log = $log;
		$this->table_prefix = $table_prefix;

		if (!defined('REGISTER_LOG_TABLE'))
		{
			define ('REGISTER_LOG_TABLE', $this->table_prefix . 'register_log');
		}
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'sheer/stopregister',
			'lang_set' => 'stopregister',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function posting_before($event)
	{
		$post_data = $event['post_data'];

		if ($post_data['poster_id'] == ANONYMOUS)
		{
			$user_ip = $this->user->data['session_ip'];
			if (!$this->config['enable_stopforumspam'])
			{
				return;
			}

			$ch_data = array($post_data['username'], $user_ip);
			$result = $this->check_stopforumspam($ch_data);

			if (sizeof($result))
			{
				foreach ($result as $key => $value)
				{
					if ($value == 'yes')
					{
						trigger_error('SPAM');
					}
				}
			}
		}
	}

	public function chk_user_data($event)
	{
		if (!$this->config['enable_stopforumspam'])
		{
			return;
		}

		$user_row = $event['data'];
		$user_row['ip'] = $this->user->data['session_ip'];
		$error = $event['error'];
		$log_data = false;
		$report = array('ip' => $this->user->lang['IP_STOP'], 'username' => $this->user->lang['NICK_STOP'], 'email' => $this->user->lang['EMAIL_STOP']);
		if ($this->config['enable_register_log'])
		{
			$log = array('ip' => 'IP_BLACKLIST', 'username' => 'NICK_BLACKLIST', 'email' => 'EMAIL_BLACKLIST');
			$this->phpbb_log->set_log_table(REGISTER_LOG_TABLE);
		}

		$ch_data = array(
			($this->config['check_username']) ? $user_row['username'] : '',
			($this->config['check_ip']) ?  $user_row['ip'] : '',
			($this->config['check_email']) ? $user_row['email'] : '',
		);

		$result = $this->check_stopforumspam($ch_data);
		if (sizeof($result))
		{
			foreach ($result as $key => $value)
			{
				if ($value == 'yes')
				{
					$error[] = $report[$key];
					$log_data = array($user_row[$key]);
					if ($this->config['enable_register_log'])
					{
						$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->data['session_ip'], $log[$key], time(), $log_data);
					}
					$error[] = sprintf($this->user->lang['REPORT_STOP'], '<a href="mailto:' . htmlspecialchars($this->config['board_contact']) . '">', '</a>');
					$event['error'] = $error;
				}
			}
		}
	}

	public function add_sql_where($event)
	{
		if($usearch = $this->request->variable('usearch', '', true))
		{
			$this->template->assign_var('USEARCH', $usearch);
			$event['sql_additional'] .= " AND u.username_clean " . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), utf8_clean_string($usearch))) . ' ';
		}

		if($isearch = $this->request->variable('isearch', ''))
		{
			$this->template->assign_var('ISEARCH', $isearch);
			$event['sql_additional'] .= " AND l.log_ip " . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), $isearch)) . ' ';
		}

		if ($asearch =  $this->request->variable('asearch', ''))
		{
			$event['sql_additional'] .= ($asearch != 'ACP_LOGS_ALL') ? ' AND l.log_operation LIKE \''. $asearch .'\'' : '';
			$this->template->assign_var('ASEARCH', $asearch);
		}
	}

	private function check_stopforumspam($chk_data)
	{
		$result = array();
		$chk_data[0] = str_replace(' ', '%20', $chk_data[0]);
		$insp = array();
		if ($chk_data[0] != '' || $chk_data[1] != '' || $chk_data[2] != '')
		{
			$xmlUrl = 'http://api.stopforumspam.org/api?';
			$xmlUrl .= (!empty($chk_data[0])) ? 'username=' . urlencode(iconv('GBK', 'UTF-8', $chk_data[0])) . '&' : '';
			$xmlUrl .= (!empty($chk_data[1])) ? 'ip=' . $chk_data[1] . '&' : '';
			$xmlUrl .= (!empty($chk_data[2])) ? 'email=' . $chk_data[2] . '' : '';
			$xmlUrl .= '&serial';

			// Try to get data from stopforumspam
			$xmlStr = @file_get_contents($xmlUrl);
			if (!$xmlStr)
			{
				// Fail get data via file_get_contents() - just try use curl
				$xmlStr = $this->get_file($xmlUrl);
			}

			$data = unserialize($xmlStr);
			if ($data['success'])
			{
				$result['username'] = (isset($data['username']['appears']) && $data['username']['appears']) ? 'yes' : 'no';
				$result['ip'] = (isset($data['ip']['appears']) && $data['ip']['appears']) ? 'yes' : 'no';
				$result['email'] = (isset($data['email']['appears']) && $data['email']['appears']) ? 'yes' : 'no';
			}
		}

		return $result;
	}

	// use curl to get response from SFS
	private function get_file($url)
	{
		// We'll use curl..most servers have it installed as default
		if (function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$contents = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			// if nothing is returned (SFS is down)
			if ($httpcode != 200)
			{
				return false;
			}

			return $contents;
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_NEED_CURL', time());

		return false;
	}

	public function add_acp_config($event)
	{
		$mode = $event['mode'];
		$display_vars = $event['display_vars'];
		if ($mode == 'registration')
		{
			$count = 0;
			foreach($display_vars['vars'] as $key => $value)
			{
				if (strripos($key, 'legend') === 0)
				{
					$count++;
				}
			}
			$next = $count + 1;
			$display_vars['vars']['legend' . $count . ''] = 'ACP_STOPFORUMSPAM';
			$display_vars['vars']['enable_stopforumspam'] = array('lang' => 'ALLOW_STOPFORUMSPAM','validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true);
			$display_vars['vars']['check_ip'] = array('lang' => 'CHECK_SPAM_IP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false);
			$display_vars['vars']['check_username'] = array('lang' => 'CHECK_USERNAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false);
			$display_vars['vars']['check_email'] = array('lang' => 'CHECK_SPAM_EMAIL', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false);
			$display_vars['vars']['enable_register_log'] = array('lang' => 'ALLOW_REG_LOG', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false);
			$display_vars['vars']['legend' . $next . ''] = 'ACP_SUBMIT_CHANGES';
			$event['display_vars'] = $display_vars;
		}
	}

	public function add_log_register($event)
	{
		// To do - delete user if this is spamer, but sucess register
		if ($this->config['enable_register_log'])
		{
			$log_data = array();
			$user_row = $event['user_row'];
			$this->phpbb_log->set_log_table(REGISTER_LOG_TABLE);
			$this->phpbb_log->add('admin', $this->user->data['user_id'], $user_row['user_ip'], 'REGISTER_SUCSESS', $user_row['user_regdate'], array($user_row['username']));
		}
	}
}
