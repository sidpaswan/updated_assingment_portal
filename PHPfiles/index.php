<?php
include 'phpConfig.php';
# session check to ensure this page isn't accessible if a session already exists
session_start();
if(isset($_SESSION['user'])){
	if($_SESSION['user'] == "admin"){
		header("Location: admin.php");
	}else{
		header("Location: myhome.php");
	}
	exit;
}
function create_captcha($text,$base_dir){
	$width = 200;
	$height = 100;
	$font ="../capt/ASMAN.TTF";
	
	$image = imagecreatetruecolor($width,$height);
	
	$white = imagecolorallocate($image,255,255,255);
	$black = imagecolorallocate($image,0,0,0);
	
	imagefill($image,0,0,$white);	
  	imagettftext($image, 45, 7, $width/7, $height/1.5, $black, $font, $text);
  	
  	$warped_image = imagecreatetruecolor($width, $height);
	imagefill($warped_image,0,0,imagecolorallocate($warped_image,255,255,255));
	
	
	for($x=0; $x<$width; $x++){
		for($y=0; $y<$height; $y++){
			$index = imagecolorat($image,$x,$y);
			$color_comp = imagecolorsforindex($image,$index);
			
			$color = imagecolorallocate($warped_image,$color_comp['red'], $color_comp['green'],$color_comp['blue']);
			
			$imageX = $x;
			$imageY = $y + sin($x/3)*5;
			
			imagesetpixel($warped_image,$imageX,$imageY,$color);
		}
	}
  	
  	//header('Content-type: image/jpeg');
  	
  	imagejpeg($warped_image,'../capt/captcha.jpg');
  	imagedestroy($warped_image);
  	
  	return $path;
}
$filename='../capt/'.session_id();
$text=rand(10000,99999);

file_put_contents($filename,$text);
$cap = create_captcha($text,$base_dir);

?>
<html>
<head>
 <title>Login page</title>
 <!-- Captcha reload button-->
 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" integrity="sha384-SZXxX4whJ79/gErwcOYf+zWLeJdY/qpuqC4cAa9rOGUstPomtqpuNWT9wdPEn2fk" crossorigin="anonymous">
 	<style>
 		#sub{
			background-color:#21209C;
			color:#F1F1F1;
 			border-radius:1px;
 			border: 2px solid black;
 			font-size:large;
 			cursor:pointer;
 			padding:10px;
 			background-color:
 		}
		input{
			border:1px black solid;
		}
		.mother{
			display:flex;
			align-items:center;
		}
	</style>
</head>
<body>
<center>
<h2>Submission Portal</h2>

<form action="login.php" method="post" id="login_form">
<table>
	<TR>
		<TD align=left>Login:</TD>
		<TD align=center>
			<input type="text" name="name">
		</TD>
	</TR>
	<TR>
		<TD align=left>Passwd:</TD>
		<TD align=center>
			<input type="PASSWORD" name="password">
		</TD>
	</TR>
	
	<!-- Captcha starts here-->
	<tr>
		<td colspan="2">
		<div class="mother">
			<!-- Captcha input box-->
			<div id="user-input" class="inline">
				<input type="text" id="cap-ent" name="cap-code" placeholder="Captcha code" />
			</div>
		 	<!-- Captcha view box-->
		    	<divclass="inline"><img src="../capt/captcha.jpg" /></div>
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p align="left" id="key" style="color:white;">Please Try Again!</p>
			
		</td>
	</tr>
	<!-- Captcha ends here-->
	
	<tr height=3>
		<td colspan="2" align="right">
			<input type="submit" name="log" value="login">
		</td>
	</tr>
	<tr>
		<td colspan=2 align="right"><br>
		<a href="resetPass.php"> Forgot Password?</a>
		</td>
	</tr>
</table>
</form>

</center>
<br>
<br>
<ul>
<li>Your roll no will be your login id. </li>
<li>No password is set initially. You can login directly. You must change your password after logging into the system.</li>
<li>You can upload one file at a time. Filename must match with the name given in the assignment.</li>
<li>You can upload a file any number of times till the deadline. Only the last submission will be treated as final submission.</li>
<li>There are two deadlines for each assignment. <b>After the first deadline, submissions will be considered as LATE.</b></li>
<li><b>After the second deadline, no submission will be undertaken for the assignment.</b></li>
<li>Please keep a copy of your file with you. You cannot download the file from this server.</li>
</ul>
<br>
</pre>
<ul>
</ul>
<p>
<br>
</body>
</html>
