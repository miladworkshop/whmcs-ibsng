<?php
require_once 'ibsng.class.php';

function ibsng_ConfigOptions() {
	$configarray = array(
		"نام گروه" 							=> array("Type" => "text", "Size" => "25", "Description" => "یک گروه در IBSng ایجاد کرده و نام آن را وارد کنید"),
		"پیشوند نام کاربری" 				=> array("Type" => "text", "Size" => "25", "Description" => "پیشوند دلخواه برای نام کاربری ( مثال : uk- )"),
		"طول نام کاربری" 					=> array("Type" => "text", "Size" => "10", "Description" => "پیشفرض : 8"),
		"طول کلمه عبور" 					=> array("Type" => "text", "Size" => "10", "Description" => "پیشفرض : 8"),
		"ترکیب نام کاربری" 					=> array('Type' => "dropdown", "Options" => "LowercaseAlphabet,UppercaseAlphabet,Numeric,AlphabetNumeric"),
		"ترکیب کلمه عبور" 					=> array('Type' => "dropdown", "Options" => "Numeric,LowercaseAlphabet,UppercaseAlphabet,AlphabetNumeric"),
		"دسترسی کاربران به پنل Internet" 	=> array("Type" => "yesno", "Description" => "دسترسی کاربران جهت ورود به پنل Internet"),
		"دسترسی کاربران به پنل VOIP" 		=> array("Type" => "yesno", "Description" => "دسترسی کاربران جهت ورود به پنل VOIP"),
		"نمایش وضعیت اکانت" 				=> array("Type" => "yesno", "Description" => "نمایش وضعیت آنلاین / آفلاین بودن اکانت"),
		"امکان تغییر کلمه عبور" 			=> array("Type" => "yesno", "Description" => "دسترسی کاربران جهت تغییر کلمه عبور اکانت"),
		"مقدار اعتبار ( Credit )" 			=> array("Type" => "text", "Size" => "10", "Description" => "UNITS"),
		"نمایش مقدار اعتبار ( Credit )" 	=> array("Type" => "yesno", "Description" => "نمایش مقدار اعتبار ( Credit ) در پنل کاربری"),
		"واحد اعتبار ( Credit )" 			=> array("Type" => "text", "Size" => "10", "Description" => "واحد نمایشی اعتبار در پنل کاربری"),
	);

	return $configarray;
}

function random($length, $type) {
	
	if(empty($length) || $length == 0)
		$length = 8;
	
	if (empty($type))
		$type = "AlphabetNumeric";
	
	switch($type) {
		case 'Numeric':
			$characters = '0123456789';
			break;
		case 'LowercaseAlphabet':
			$characters = 'abcdefghijklmnopqrstuvwxyz';
			break;
		case 'UppercaseAlphabet':
			$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		case 'AlphabetNumeric':
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
	}
	
	$charactersLength 	= strlen($characters);
	$randomString 		= '';
	
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	
	return $randomString;
}

function userStatusCheck($serverusername, $serverpassword, $serverip, $user) {
	
	$IBSng 					= new IBSng($serverusername, $serverpassword, $serverip, 'admin');
	
	$status 				= $IBSng->userStatus($user);
	
	$userStatus 			= str_replace('Offline', '<span style="color:#7F0000;">آفلاین</span>', $status);
	$userStatus 			= str_replace('Online', '<span style="color:#00A310;">آنلاین</span>', $userStatus);
	$userStatus 			= str_replace('Unknown', '<span style="color:#404040;">نامشخص</span>', $userStatus);
	
	return $userStatus;
}

function userGetCredit($serverusername, $serverpassword, $serverip, $user) {
	
	$IBSng 					= new IBSng($serverusername, $serverpassword, $serverip, 'user');
	
	$status 				= $IBSng->userCredit();

	return $status;
}

function ibsng_ClientArea($params) {
	
	$ibsng_access_i 		= $params['configoption7'];
	$ibsng_access_v 		= $params['configoption8'];
	
	$ibsng_access_status 	= $params['configoption9'];
	$ibsng_access_change_pw = $params['configoption10'];
	$ibsng_access_credit 	= $params['configoption12'];

	$http = ($params['serversecure'] ? "https" : "http");
	$host = ($params['serverhostname'] ? $params['serverhostname'] : $params['serverip']);
	
	$code = 'اطلاعات اکانت شما به شرح زیر می باشد<br /><br /><table class="table table-striped table-bordered"><tbody>';
	
	if ($ibsng_access_status == true)
	{
		$code .= '<tr>
			<td style="width:50%; text-align:right; direction:rtl;">وضعیت</td>
			<td style="width:50%; text-align:right; direction:rtl;">'. userStatusCheck($params['serverusername'], $params['serverpassword'], $params['serverip'], $params['username']) .'</td>
		</tr>';
	}
	
	if ($ibsng_access_credit == true)
	{
		$code .= '<tr>
			<td style="width:50%; text-align:right; direction:rtl;">اعتبار ( Credit )</td>
			<td style="width:50%; text-align:right; direction:rtl;">'. userGetCredit($params['username'], $params['password'], $params['serverip'], $params['username']) .' '. $params['configoption13'] .'</td>
		</tr>';
	}

	$code .= '<tr>
				<td style="width:50%; text-align:right; direction:rtl;">آیپی / آدرس</td>
				<td style="width:50%; text-align: left; direction:ltr;">'. $host .'</td>
			</tr>
			<tr>
				<td style="width:50%; text-align:right; direction:rtl;">نام کاربری</td>
				<td style="width:50%; text-align: left; direction:ltr;">'. $params['username'] .'</td>
			</tr>
			<tr>
				<td style="width:50%; text-align:right; direction:rtl;">کلمه عبور</td>
				<td style="width:50%; text-align: left; direction:ltr;">'. $params['password'] .'</td>
			</tr>
		</tbody>
	</table>';
	
	if ($ibsng_access_i == true || $ibsng_access_v == true) {
		$code .= "<table class='table table-bordered'>
			<tbody>
				<tr>";
				
				if ($ibsng_access_i == true) {
					$code .= "<td style='width:50%;'>
						<form action=\"" . $http . "://" . $host . "/IBSng/user/\" method=\"POST\" target=\"_blank\">
							<input type=\"hidden\" name=\"normal_username\" value=\"" . $params['username'] . "\">
							<input type=\"hidden\" name=\"normal_password\" value=\"" . $params['password'] . "\">
							<input type=\"submit\" value=\"ورود به کنترل پنل IBSng\" style='direction:rtl;' class='btn btn-primary'>
						</form>
					</td>";
				}
				
				if ($ibsng_access_v == true) {
					$code .= "<td style='width:50%;'>
						<form action=\"" . $http . "://" . $host . "/IBSng/user/\" method=\"POST\" target=\"_blank\">
							<input type=\"hidden\" name=\"voip_username\" value=\"" . $params['username'] . "\">
							<input type=\"hidden\" name=\"voip_password\" value=\"" . $params['password'] . "\">
							<input type=\"submit\" value=\"ورود به کنترل پنل VOIP\" style='direction:rtl;' class='btn btn-primary'>
						</form>
					</td>";
				}

				$code .= "</tr>
			</tbody>
		</table>";
	}

	if ($ibsng_access_change_pw == true)
	{
		$code .='<br /><hr />تغییر کلمه عبور<br /><br />';
		
		if (isset($_POST['do']) && $_POST['do'] == 'IBSngChangePassword')
		{
			if (empty($_POST['cpOld']) || empty($_POST['cpNew']) || empty($_POST['cpRNew']))
			{
				$code .= "<div class='alert alert-danger'>به منظور تغییر کلمه عبور کلیه فیلدها را پر کنید</div>";
			} else {
				if ($_POST['cpNew'] == $_POST['cpRNew'])
				{
					
					$oldPassword 	= $_POST['cpOld'];
					$newPassword 	= $_POST['cpNew'];
					
					$IBSng 			= new IBSng($params['username'], $oldPassword, $params['serverip'], 'user');
					
					$command 		= $IBSng->changePassword($oldPassword, $newPassword, $newPassword);
					
					if ($command == "success")
					{
						$code .= "<div class='alert alert-success'>کلمه عبور اکانت شما با موفقیت تغییر یافت, صفحه را مجدداً بارگذاری کنید</div>";
						
						update_query("tblhosting", array("password" => encrypt($newPassword)), array("id" => $params['serviceid']));
					} else {
						$code .= "<div class='alert alert-danger'>خطا ". $command ."</div>";
					}
				} else {
					$code .= "<div class='alert alert-danger'>کلمه عبور جدید با تکرار آن برابر نیست</div>";
				}
			}
		}
		
		$code .= '<form action="" method="post">
		
		<input type="hidden" name="do" value="IBSngChangePassword">
		
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td style="width:25%; text-align:right; direction:rtl;">کلمه عبور فعلی</td>
						<td style="width:75%; text-align: left; direction:ltr;"><input type="text" class="form-control" name="cpOld" value="'. $params['password'] .'"></td>
					</tr>
					<tr>
						<td style="width:25%; text-align:right; direction:rtl;">کلمه عبور جدید</td>
						<td style="width:75%; text-align: left; direction:ltr;"><input type="password" class="form-control" name="cpNew"></td>
					</tr>
					<tr>
						<td style="width:25%; text-align:right; direction:rtl;">تکرار کلمه عبور جدید</td>
						<td style="width:75%; text-align: left; direction:ltr;"><input type="password" class="form-control" name="cpRNew"></td>
					</tr>
					<tr>
						<td style="width:25%; text-align:right; direction:rtl;">&nbsp;</td>
						<td style="width:75%; text-align: left; direction:ltr;"><button type="submit" class="btn btn-primary">تغییر کلمه عبور اکانت</button></td>
					</tr>
				</tbody>
			</table>
		</form>';
	}
	
	return $code;
}


function ibsng_AdminLink($params) {
	$http = ($params['serversecure'] ? "https" : "http");
	$host = ($params['serverhostname'] ? $params['serverhostname'] : $params['serverip']);
	$code = "<form action=\"" . $http . "://" . $host . "/IBSng/admin/\" method=\"POST\" target=\"_blank\">
	<input type=\"hidden\" name=\"username\" value=\"" . $params['serverusername'] . "\">
	<input type=\"hidden\" name=\"password\" value=\"" . $params['serverpassword'] . "\">
	<input type=\"submit\" value=\"ورود به پنل مدیریت IBSng\">
	</form>";

	return $code;
}

function ibsng_CreateAccount($params) {
	
	$IBSng 					= new IBSng($params['serverusername'], $params['serverpassword'], $params['serverip'], 'admin');
	
	$serviceid 				= $params['serviceid'];

	$group 					= $params['configoption1'];
	$refix 					= $params['configoption2'];
	$username_length 		= $params['configoption3'];
	$password_length 		= $params['configoption4'];
	
	$rendom_username_type 	= $params['configoption5'];
	$rendom_password_type 	= $params['configoption6'];
	
	$ibsng_access_i 		= $params['configoption7'];
	$ibsng_access_v 		= $params['configoption8'];
	
	$ibsng_access_status 	= $params['configoption9'];
	$ibsng_access_change_pw = $params['configoption10'];

	if (empty($params['configoption11'])) {
		$credit 				= 1;
	} else {
		$credit 				= $params['configoption11'];
	}
	
	$password 				= random($password_length, $rendom_password_type);
	$username 				= $refix . random($username_length, $rendom_username_type);
	
	$command 				= $IBSng->addUser($group, $username, $password, $credit);
	
	if ($command == "success")
	{
		mysql_query('UPDATE tblhosting SET username = \'' . $username . '\' WHERE id = ' . $serviceid . ' ; ');
		mysql_query('UPDATE tblhosting SET password = \'' . encrypt($password) . '\' WHERE id = ' . $serviceid . ' ; ');
		
		return 'success';
	} else {
		return $command;
	}
}

function ibsng_TerminateAccount($params) {
	
	$IBSng 					= new IBSng($params['serverusername'], $params['serverpassword'], $params['serverip'], 'admin');
	
	$serviceid 				= $params['serviceid'];

	$group 					= $params['configoption1'];
	
	$password 				= $params['password'];
	$username 				= $params['username'];
	
	$command 				= $IBSng->removeUser($username);
	
	if ($command == "success")
	{
		return 'success';
	} else {
		return $command;
	}
}


function ibsng_SuspendAccount($params) {

	$IBSng 					= new IBSng($params['serverusername'], $params['serverpassword'], $params['serverip'], 'admin');

	$username 				= $params['username'];
	
	$command 				= $IBSng->lockUser($username);
	
	if ($command == "success")
	{
		return 'success';
	} else {
		return $command;
	}
}


function ibsng_UnsuspendAccount($params) {

	$IBSng 					= new IBSng($params['serverusername'], $params['serverpassword'], $params['serverip'], 'admin');

	$username 				= $params['username'];
	
	$command 				= $IBSng->unlockUser($username);
	
	if ($command == "success")
	{
		return 'success';
	} else {
		return $command;
	}
}

if (!defined( "WHMCS" )) {
	exit( "This file cannot be accessed directly" );
}
?>