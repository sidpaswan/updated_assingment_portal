<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function send_mail($message,$reciever){
$mail = new PHPMailer(true);
	try {
	    //Server settings
	    $mail->isSMTP(true);                                            //Send using SMTP
	    $mail->CharSet 	  = 'utf-8';
	    $mail->Host       = 'smtp.outlook.com';                     //Set the SMTP server to send through
	    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
	    $mail->Username   = '<Your Email Here>';                     //SMTP username
	    $mail->Password   = '<Password Here>';                               //SMTP password
	    $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
	    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

	    //Recipients
	    $mail->From = 'sidiniitp@outlook.com';
	    $mail->FromName='Admin';
	    $mail->addAddress($reciever);

	    //Content
	    $mail->isHTML(true);                                  //Set email format to HTML
	    $mail->Subject = 'Reset Password Link';
	    $mail->Body    = $message;

	    $mail->send();
	    echo 'Message has been sent';
	} catch (Exception $e) {
	    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
}

include "phpConfig.php";
session_start();
if(isset($_SESSION['user'])) {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
function file_check($pass,$base_dir){
    $pass_list_loc = $base_dir.'/plist.txt';
    $myfile = fopen($pass_list_loc,'r') or die('Unable to open the file');
    $valid = false;

    while(($buffer = fgets($myfile))!== false){
        if(strpos($buffer,$pass) !== false){
            $valid = true;
            break;
        }
    }
    fclose($myfile);
    return $valid;
}
function generate_token(){
	$length = 16;
	$token = bin2hex(random_bytes($length));
	return $token;
}

# Get current URL
function get_url(){
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
		 $url = "https://";   
	else  
		 $url = "http://";      

	$url.= $_SERVER['HTTP_HOST'];   
	$url.= $_SERVER['REQUEST_URI'];    
	  
	return $url;  
}

# When we update the password
if(isset($_POST['u_pass'])){
	$verification_token = filter_var($_POST['post_token'], FILTER_SANITIZE_STRING);
	$verify = shell_exec("cat $dbfile | grep -F -m 1 $verification_token");
	if($verify && time() <= explode(':',$verify)[5] ){
		$new_p = $_POST['newPass'];		# get new password entered
		$new_p_check = $_POST['confPass'];	# get re-typed new password

		# if the new passwords do not match, alert the user
		if($new_p != $new_p_check){	
			echo "<script>alert('The passwords do not match! Please try again')</script>";
		}else{
			$characters = preg_match('@[A-Z,a-z]@',$new_p);		# check if the password conatins alphabet
			$spec_char = preg_match('@[^\w]@',$new_p);		# check if the password contains special charactrers
			$num = preg_match('@[0-9]@',$new_p);			# check if the password contains a number
			
			# check if the password has alphabet, special character, number, and is atleast of length 8
			if(!$characters || !$spec_char || !$num || strlen($new_p)<8){
				# if not then alert the user
				echo '<script>alert("The password should be at least 8 characters in length and should have special characters and number in it.")</script>';
			}else{
				$val = file_check($new_p,$base_dir);
				if($val){
					echo '<script>alert("The new password is part of leaked passwords dictionary. Please use another password and try again!")</script>';
				}else{
					$roll=explode(':',$verify)[0];
					$ret = shell_exec("$shscripts/gen_token.sh $roll $new_p -o");
					header("Location:index.php");
				}
			}
		}
	}else{
		echo "Invalid Password reset link<br>";
		if( time() > explode(':',$verify)[5] )
			echo "Token Expired! Generate New Token !<br>";
		echo "<a href='resetPass.php'> Go Back </a>";
	}
}
#When we generate the token
if(isset($_POST['gen_token'])){
	$roll = filter_var($_POST['roll'], FILTER_SANITIZE_STRING);
	$roll_string = shell_exec("cat $dbfile | grep -Fi -m 1 $roll");
	$roll = explode(":",$roll_string)[0];
	$reciever = explode(":",$roll_string)[4];
	
	if($roll){
		$tok = generate_token();
		$op = shell_exec("$shscripts/gen_token.sh $roll $tok");
		if($op)
			echo "<p align='center'>Could Not Generate! Please Try again!</p><br>";
		else{
			$message = "This is your password reset link and is valid for 1 hour only! <br><a href='".get_url()."?token=".$tok."'> Go to Link </a>";
			send_mail($message,$reciever);
		}
	}	
}

?>
<html>
<head>
	<style>
        body{
        	height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        form{
            display: flex;
            flex-direction: column;
            justify-content: center;
            width:50%;
        }
        #bar_back{
            width:50%;
            background-color:#ddd;
        }
        #bar_front{
            width: 1%;
            height:10px;
            background-color: red;
        }
    </style>
</head>

<body>
	<a href="index.php"> Home </a>
	<?php if($_GET['token']){ ?>
	<form action="resetPass.php" method="post"> 
		<input type="text" value="<?php echo $_GET['token'];?>" name="post_token" readonly hidden><br>
		<label for="newPass">Enter New Password: </label>
		<input type="password" name="newPass" onkeyup="print_pass()" id="pass"><br>
		<label for="confPass">Re-Enter Password: </label>
		<input type="password" name="confPass"><br>
		<input type="submit" name="u_pass" value="Update Password"><br>
	</form>
	<div id="bar_back">
		<div id="bar_front"></div>
	</div>
	<?php }else {?>
	<form action="resetPass.php" method="post">
		<label for="roll">Enter your Roll Number </label>
		<input type="text" name="roll"><br>
		<input type="submit" name="gen_token" value="Reset Password">
	</form>
	<?php }?>


<script>
    function print_pass(){
        let strongPassword = new RegExp('(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})')
        let mediumPassword = new RegExp('((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,}))|((?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,}))|((?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,}))')
        var but = document.getElementById('uP');

        var varPass = document.getElementById('pass').value;
        var pLen = varPass.length;
        
        var b1 = strongPassword.test(varPass);
        var b2 = mediumPassword.test(varPass);
        var elem = document.getElementById('bar_front');
        if( b1 ){
            elem.style.width='100%';
            elem.style.background='green';
        }else if( b2 ){
            elem.style.width='66%';
            elem.style.background='yellow';
        }else{
            elem.style.width='20%';
            elem.style.background='red';
        }

    }
</script>
</body>
</html>
