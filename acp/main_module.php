<?php
/**
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopregister\acp;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $template, $request, $table_prefix, $user, $phpbb_log, $phpbb_container, $config, $phpbb_root_path, $phpEx;

		$user->add_lang('mcp');

		$log_table = $phpbb_container->getParameter('tables.register_log');

		$phpbb_log->set_log_table($log_table);

		$whois		= $request->variable('whois', false);

		$start		= $request->variable('start', 0);
		$deletemark	= $request->variable('delmarked', false, false, \phpbb\request\request_interface::POST);
		$deleteall	= $request->variable('delall', false, false, \phpbb\request\request_interface::POST);
		$marked		= $request->variable('mark', array(0));
		$asearch	= $request->variable('asearch', 'ACP_LOGS_ALL');
		//print "$asearch<br />";

		// Sort keys
		$sort_days	= $request->variable('st', 0);
		$sort_key	= $request->variable('sk', 't');
		$sort_dir	= $request->variable('sd', 'd');

		$pagination = $phpbb_container->get('pagination');

		if ($whois)
		{
			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

			$user->add_lang('acp/users');
			$this->page_title = 'WHOIS';
			$this->tpl_name = 'simple_body';
			$ip = $request->variable('ip', '');
			$domain = gethostbyaddr($ip);
			$ipwhois = user_ipwhois($ip);

			$template->assign_vars(array(
				'MESSAGE_TITLE'		=> sprintf($user->lang['IP_WHOIS_FOR'], $domain),
				'MESSAGE_TEXT'		=> nl2br($ipwhois))
			);

			return;
		}

		// Delete entries if requested and able
		if (($deletemark || $deleteall))
		{
			if (confirm_box(true))
			{
				$conditions = array();
				$sql_where = ($asearch === 'ACP_LOGS_ALL') ? '' : ' AND log_operation = \'' . $asearch . '\'';

				if ($deletemark && sizeof($marked))
				{
					$sql = 'DELETE FROM ' . $log_table . '
						WHERE ' . $db->sql_in_set('log_id', $marked) . $sql_where;
				}

				if ($deleteall)
				{
					$sql = 'TRUNCATE TABLE ' . $log_table;
				}

				$db->sql_query($sql);
				$phpbb_log->set_log_table(LOG_TABLE);
				add_log('admin', 'LOG_CLEAR_REGISTER_LOG', $user->data['username']);
				redirect($this->u_action);
			}
			else
			{
				confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
					'start'		=> $start,
					'delmarked'	=> $deletemark,
					'delall'	=> $deleteall,
					'mark'		=> $marked,
					'st'		=> $sort_days,
					'sk'		=> $sort_key,
					'sd'		=> $sort_dir,
					'i'			=> $id,
					'mode'		=> $mode,
					'asearch'	=> $asearch,
					))
				);
			}
		}

		// Sorting
		$limit_days = array(0 => $user->lang['ALL_ENTRIES'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);
		$sort_by_text = array('u' => $user->lang['SORT_USERNAME'], 't' => $user->lang['SORT_DATE'], 'i' => $user->lang['SORT_IP'], 'o' => $user->lang['SORT_ACTION']);
		$sort_by_sql = array('u' => 'u.username_clean', 't' => 'l.log_time', 'i' => 'l.log_ip', 'o' => 'l.log_operation');

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

		// Define where and sort sql for use in displaying logs
		$sql_where = ($sort_days) ? (time() - ($sort_days * 86400)) : 0;
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$this->tpl_name = 'acp_register_logs_body';
		$this->page_title = $user->lang('ACP_REGISTER_LOGS');

		$log_data = array();
		$log_count = 0;
		$start = view_log('admin', $log_data, $log_count, $config['topics_per_page'], $start, 0, 0, 0, $sql_where, $sql_sort);

		$base_url = $this->u_action . "&amp;$u_sort_param";
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $log_count, $config['topics_per_page'], $start);

		foreach ($log_data as $row)
		{
			$template->assign_block_vars('log', array(
				'USERNAME'	=> $row['username_full'],
				'IP'		=> $row['ip'],
				'DATE'		=> $user->format_date($row['time']),
				'ACTION'	=> $row['action'],
				'ID'		=> $row['id'],
				'U_IP'		=> (!empty($row['ip'])) ? $this->u_action . '&amp;whois=true&amp;ip=' . $row['ip'] . '' : '',
				)
			);
		}

		// Actions sorting
		$list_actions = array(
			'ACP_LOGS_ALL',
			'IP_BLACKLIST',
			'NICK_BLACKLIST',
			'EMAIL_BLACKLIST',
			'REGISTER_SUCSESS',
			'EXCEED_REGISTERS',
		);

		$s_asearch = '';
		foreach($list_actions as $key => $action)
		{
			$selected = ($action == $asearch) ? ' selected="selected"' : '';
			$s_asearch .= '<option value="' . $action . '"' . $selected . '>' . str_replace('%1$s', '', $user->lang[$action]) . '</option>';
		}

		$template->assign_vars(array(
				'U_ACTION'		=> $this->u_action . "&amp;start=$start",
				'S_LIMIT_DAYS'	=> $s_limit_days,
				'S_SORT_KEY'	=> $s_sort_key,
				'S_SORT_DIR'	=> $s_sort_dir,
				'S_ASEARCH'		=> $s_asearch,
			)
		);
	}
}
