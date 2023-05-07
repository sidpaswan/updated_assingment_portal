<?php
include "phpConfig.php";
session_start();
if(!isset($_SESSION['user'])) {
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<style>
        body{
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
<?php 
$userid=$_SESSION['user'];
echo "<title>Changing passwd for $userid</title>\n";
echo "<H1>Welcome $userid</H1><br>";
$output=shell_exec("$shscripts/gen_option.sh $userid");
echo "$output<br>";
echo "<b>Changing passwd for user .. $userid</b><br><br>\n";
// update 3 here
echo "<FORM action=\"chpasswd.php\" method=\"post\">\n";
/// update 3 ends here
echo "<TABLE BORDER=0>\n";
echo "<TR>\n";
echo "<TD align=left>Old passwd:</TD>\n";
echo "<TD align=left><input type=\"PASSWORD\" name=\"oldpasswd\"></TD>\n";
echo "</TR>\n";
echo "<TR>\n";
echo "<TD align=left>New passwd:</TD>\n";
echo "<TD align=left><input type=\"PASSWORD\" name=\"newpasswd1\" id=\"pass\" onkeyup=\"print_pass()\"></TD>\n";
echo "</TR>\n";
echo "<TR>\n";
echo "<TD align=left>Re Type New passwd:</TD>\n";
echo "<TD align=left><input type=\"PASSWORD\" name=\"newpasswd2\"></TD>\n";
echo "</TR>\n";
echo "</TABLE>\n";
echo "<input type=\"hidden\" name=\"uname\" value=\"$userid\">\n";
echo "<input type=\"submit\" value=\"Submit\" name=\"submit\">\n";
echo "</FORM>\n";
// strength bar
echo '<div id="bar_back">';
echo 	'<div id="bar_front"></div>';
echo '</div>';
// update 1 here 
# 1. added instructions for updating the password
echo "<ul>";
echo"<li>The password should be at least 8 characters</li>";
echo "<li>The password should contain at least 1 character. Mix Both Cases for better security. </li>";
echo "<li>The password should contains at least 1 number.</li>";
echo "<li>The password should contain at least 1 special character.</li>";
echo "</ul>"
// 1 ends here
?>
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

<?php
// Update 2 here

if(isset($_POST['submit'])){			# check if the user has submit the form
	$oldpass = $_POST['oldpasswd']; 	# get the old password entered in the form
	$new_p = $_POST['newpasswd1'];		# get new password entered
	$new_p_check = $_POST['newpasswd2'];	# get re-typed new password

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
				# else set the session variables and pass them onto the password update page
				$_SESSION['oldpass'] = $oldpass;
				$_SESSION['newpasswd1'] = $new_p;
				$_SESSION['newpasswd2'] = $new_p_check;
				header('Location: checkAndUpdate.php');
			}
		}
	}
}
# 2 ends here
?>
