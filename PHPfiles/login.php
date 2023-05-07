
<?php 
	session_start();
	include "phpConfig.php";
	function get_capt_code(){
		return $capt_val = file_get_contents('../capt/'.session_id());
	}
	$username=$_POST['name']; 
	$passwd=$_POST['password']; //using https
	
	$verify = get_capt_code();
	unlink('../capt/'.session_id());
	session_destroy();

	if($_POST['cap-code']!=$verify){
		echo "Invalid Captcha!";
		echo "<a href='index.php'>Home</a>";
	}else{		
		$db_cmd="flock -s $dbfile -c \"sed -n '/$username/p' $dbfile\""; 
		$user_data=shell_exec("$db_cmd");

		list($name, $en_passwd_f, $salt)=explode(":",$user_data);
		$salt=trim($salt);

		$en_passwd=md5($passwd.$salt); //better hashing with random salt

		if(trim($name) == trim($username) && trim($username) != ""){
			if (trim($en_passwd_f) == trim($en_passwd) ){
				session_start();
				$_SESSION['user']=$username;
				if($username == "admin") {
					header("Location: admin.php");
				} else {
					header("Location: myhome.php");
				}
			}else{
				echo "<br>Incorrect login name or password...";
			}
		}else{
		 echo "<br>Incorrect login name or password...";
		}
	}
?>

