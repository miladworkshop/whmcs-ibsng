<?php
class IBSng
{
	public $username;
	public $password;
	public $ip;
	
	private $handler;
	private $cookie;
	private $cookie_file = 'IBSng_cookie.txt';

	public function __construct($username, $password, $ip, $type)
	{
		$this->username 					= $username;
		$this->password 					= $password;
		$this->ip 							= $ip;
		
		if (isset($type) && empty($type)){
			
			$type 							= 'admin';
			$post_data['username'] 			= $username;
			$post_data['password'] 			= $password;
			
		} elseif (isset($type) && $type == 'user') {
			
			$type 							= 'user';
			$post_data['lang'] 				= 'en';
			$post_data['normal_username'] 	= $username;
			$post_data['normal_password'] 	= $password;
			
		}  elseif (isset($type) && $type == 'voip') {
			
			$type 							= 'user';
			$post_data['lang'] 				= 'en';
			$post_data['voip_username'] 	= $username;
			$post_data['voip_password'] 	= $password;
			
		} elseif (isset($type) && $type == 'admin') {
			
			$type 							= 'admin';
			$post_data['username'] 			= $username;
			$post_data['password'] 			= $password;
			
		} else {
			
			$type 							= 'admin';
			$post_data['username'] 			= $username;
			$post_data['password'] 			= $password;
			
		}

		$url 							= $this->ip .'/IBSng/'. $type .'/';
		$this->handler 					= curl_init();
		
		curl_setopt($this->handler, CURLOPT_URL, $url);
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($this->handler, CURLOPT_HEADER, true);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->handler, CURLOPT_COOKIEJAR, realpath($this->cookie_file));
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		
		$output = curl_exec($this->handler);

		preg_match_all('|Set-Cookie: (.*);|U', $output, $matches);
		$this->cookie = implode('; ', $matches[1]);
		
	}
	
	public function get_string_between($string, $start, $end) {
		$string = ' ' . $string;
		$ini 	= strpos($string, $start);
		if ($ini == 0) return '';
		$ini 	+= strlen($start);
		$len 	= strpos($string, $end, $ini) - $ini;
		
		return substr($string, $ini, $len);
	}
	
	public function userCredit()
	{
		$url = $this->ip . '/IBSng/user/home.php';

		$this->handler = curl_init();
		curl_setopt($this->handler, CURLOPT_URL, $url);
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		$output = curl_exec($this->handler);

		return $this->get_string_between($output, '<td class="Form_Content_Row_Right_2col_dark">', 'UNITS');
	}

	public function changePassword($old, $new1, $new2)
	{
		$url = $this->ip . '/IBSng/user/change_pass.php';

		$post_data['old_normal_password'] 		= $old;
		$post_data['new_normal_password1'] 		= $new1;
		$post_data['new_normal_password2'] 		= $new2;

		$this->handler = curl_init();
		curl_setopt($this->handler, CURLOPT_URL, $url);
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		$output = curl_exec($this->handler);
		

		if (strpos($output, 'Changed Successfully') !== false)
		{
			return "success";
			
		} elseif (strpos($output, 'Old password is wrong') !== false)
		{
			return "Old password is wrong";
			
		} elseif (strpos($output, 't match') !== false)
		{
			return "Passwords don't match";
			
		} elseif (strpos($output, 'Access Denied') !== false)
		{
			return "Access Denied";
			
		} else {
			return "Unknown Error";
		}
	}

	public function userExist($username)
	{
		$url = $this->ip . '/IBSng/admin/user/user_info.php?normal_username_multi=' . $username;
		$this->handler = curl_init();
		curl_setopt($this->handler, CURLOPT_URL, $url);
		curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		$output = curl_exec($this->handler);
		if (strpos($output, 'does not exists') !== false)
		{
			return 0;
		}
		else
		{
			$pattern1 	= 'change_credit.php?user_id=';
			$pos1 		= strpos($output, $pattern1);
			$sub1 		= substr($output, $pos1 + strlen($pattern1), 100);
			$pattern2 	= '"';
			$pos2 		= strpos($sub1, $pattern2);
			$sub2 		= substr($sub1, 0, $pos2);
			
			if (isset($sub2) && $sub2 > 0) {
				return $sub2;
			} else {
				return 0;
			}
		}
	}
	
	public function userStatus($username)
	{
		if ($this->userExist($username) > 0)
		{
			$url = $this->ip . '/IBSng/admin/user/user_info.php?normal_username_multi=' . $username;
			$this->handler = curl_init();
			curl_setopt($this->handler, CURLOPT_URL, $url);
			curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
			curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
			$output = curl_exec($this->handler);
			if (strpos($output, 'Offline') !== false)
			{
				return "Offline";
			}
			elseif (strpos($output, 'Online') !== false)
			{
				return "Online";
			} else {
				return "Unknown";
			}
		} else {
			return "User not found";
		}
	}
	
	public function lockUser($username)
	{
		if ($this->userExist($username) > 0) {
			$url 									= $this->ip . '/IBSng/admin/plugins/edit.php';
			$post_data['target'] 					= 'user';
			$post_data['target_id'] 				= $this->userExist($username);
			$post_data['update'] 					= 1;
			$post_data['edit_tpl_cs'] 				= 'lock';
			$post_data['attr_update_method_0'] 		= 'lock';
			$post_data['lock'] 						= 'Locked BY WHMCS';

			$this->handler = curl_init();
			curl_setopt($this->handler, CURLOPT_URL, $url);
			curl_setopt($this->handler, CURLOPT_POST, true);
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
			curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
			$output = curl_exec($this->handler);
			
			if ($this->userExist($username) > 0) {
				return "success";
			} else {
				return "Error Locking User";
			}
		} else {
			return "User Not Exist";
		}
	}
	
	public function unlockUser($username)
	{
		if ($this->userExist($username) > 0) {
			$url 									= $this->ip . '/IBSng/admin/plugins/edit.php';
			$post_data['target'] 					= 'user';
			$post_data['target_id'] 				= $this->userExist($username);
			$post_data['update'] 					= 1;
			$post_data['edit_tpl_cs'] 				= 'lock';
			$post_data['tab1_selected'] 			= 'Main';
			$post_data['attr_update_method_0'] 		= 'lock';
			$post_data['has_lock'] 					= 't';

			$this->handler = curl_init();
			curl_setopt($this->handler, CURLOPT_URL, $url);
			curl_setopt($this->handler, CURLOPT_POST, true);
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
			curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
			$output = curl_exec($this->handler);
			
			if ($this->userExist($username) > 0) {
				return "success";
			} else {
				return "Error Locking User";
			}
		} else {
			return "User Not Exist";
		}
	}
	
	public function addUser($group_name, $username, $password, $unit)
	{
		if ($this->userExist($username) == 0) {
			$owner 									= 'system';
			$id 									= $this->addUid($group_name, $unit);
			$url 									= $this->ip . '/IBSng/admin/plugins/edit.php?edit_user=1&user_id=' . $id . '&submit_form=1&add=1&count=1&credit='. $unit .'&owner_name=' . $owner . '&group_name=' . $group_name . '&x=35&y=1&edit__normal_username=normal_username&edit__voip_username=voip_username';
			$post_data['target'] 					= 'user';
			$post_data['target_id'] 				= $id;
			$post_data['update'] 					= 1;
			$post_data['edit_tpl_cs'] 				= 'normal_username';
			$post_data['attr_update_method_0'] 		= 'normalAttrs';
			$post_data['has_normal_username'] 		= 't';
			$post_data['current_normal_username'] 	= '';
			$post_data['normal_username'] 			= $username; // username
			$post_data['password'] 					= $password; // password
			$post_data['normal_save_user_add'] 		= 't';
			$post_data['credit'] 					= $unit;

			$this->handler = curl_init();
			curl_setopt($this->handler, CURLOPT_URL, $url);
			curl_setopt($this->handler, CURLOPT_POST, true);
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
			curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
			$output = curl_exec($this->handler);
			
			if ($this->userExist($username) > 0) {
				return "success";
			} else {
				return "User Create Error";
			}
		} else {
			return "User Exist";
		}
	}
	
	public function removeUser($username)
	{
		if ($this->userExist($username) > 0) {

			$owner 									= 'system';
			$url 									= $this->ip . '/IBSng/admin/user/del_user.php';
			$post_data['user_id'] 					= $this->userExist($username);
			$post_data['delete'] 					= '1';
			$post_data['delete_comment'] 			= 'Remove BY WHMCS';

			$this->handler = curl_init();
			curl_setopt($this->handler, CURLOPT_URL, $url);
			curl_setopt($this->handler, CURLOPT_POST, true);
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
			curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
			$output = curl_exec($this->handler);
			
			if (strpos($output, 'Deleted Successfully') !== false)
			{
				return "success";
			} else {
				return "Remove User Error";
			}
		} else {
			return "User Not Exist";
		}
	}

	private function addUid($group_name, $unit)
	{
	
		$url 								= $this->ip . '/IBSng/admin/user/add_new_users.php';
		$post_data['submit_form'] 			= 1;
		$post_data['add'] 					= 1;
		$post_data['count'] 				= 1;
		$post_data['credit'] 				= $unit;
		$post_data['owner_name'] 			= "system";
		$post_data['group_name'] 			= $group_name; // $group_name;
		$post_data['edit__normal_username'] = 'normal_username';
		
		$this->handler = curl_init();
		curl_setopt($this->handler, CURLOPT_URL, $url);
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		$output = curl_exec($this->handler);
		
		$pattern1 	= '<input type=hidden name="user_id" value="';
		$pos1 		= strpos($output, $pattern1);
		$sub1 		= substr($output, $pos1 + strlen($pattern1), 100);
		$pattern2 	= '">';
		$pos2 		= strpos($sub1, $pattern2);
		$sub2 		= substr($sub1, 0, $pos2);
		
		if (isset($sub2) && $sub2 > 0) {
			return $sub2;
		} else {
			return 0;
		}
	}
	
	public function chargeUser($group_name,$username,$password)
	{
		$id = $this->userExist($username);
		
		if($id === false)
			return $this->addUser($group_name, $username, $password);
		
		$url = $this->ip . '/IBSng/admin/plugins/edit.php';
		
		$post_data['target'] 				= 'user';
		$post_data['target_id'] 			= $id;
		$post_data['update'] 				= 1;
		$post_data['edit_tpl_cs'] 			= 'group_name';
		$post_data['tab1_selected'] 		= 'Main';
		$post_data['attr_update_method_0'] 	= 'groupName';
		$post_data['group_name'] 			= $group_name;
		
		$this->handler = curl_init();
		curl_setopt($this->handler, CURLOPT_URL, $url);
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		$output = curl_exec($this->handler);
		
		unset($post_data);
		
		$url = $this->ip . '/IBSng/admin/plugins/edit.php';
		
		$post_data['target'] 				= 'user';
		$post_data['target_id'] 			= $id;
		$post_data['update'] 				= 1;
		$post_data['edit_tpl_cs'] 			= 'rel_exp_date,abs_exp_date,first_login';
		$post_data['tab1_selected'] 		= 'Exp_Dates';
		$post_data['attr_update_method_0'] 	= 'relExpDate';
		$post_data['attr_update_method_1'] 	= 'absExpDate';
		$post_data['attr_update_method_2'] 	= 'firstLogin';
		$post_data['reset_first_login'] 	= 't';
		
		$this->handler = curl_init();
		curl_setopt($this->handler, CURLOPT_URL, $url);
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($this->handler, CURLOPT_HEADER, TRUE);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->handler, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		$output = curl_exec($this->handler);
		
		$pattern1 	= '<input type=hidden name="user_id" value="';
		$pos1 		= strpos($output, $pattern1);
		$sub1 		= substr($output, $pos1 + strlen($pattern1), 100);
		$pattern2 	= '">';
		$pos2 		= strpos($sub1, $pattern2);
		$sub2 		= substr($sub1, 0, $pos2);
		
		if (isset($sub2) && $sub2 > 0) {
			return "success";
		} else {
			return "Error ReCharge User";
		}
	}
}