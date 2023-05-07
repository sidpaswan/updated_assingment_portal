<?php
include "phpConfig.php";
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user'] != "admin") {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
?>

<?php
$assignment=$_POST['filename'];
$deadline1=$_POST['deadline1'];
$deadline2=$_POST['deadline2'];
$ta_contact=$_POST['ta_contact'];
$max_marks=$_POST['max_marks'];
$weight=$_POST['weightage'];
$late_penalty=$_POST['late_penalty'];
if($late_penalty<0){
	$late_penalty=-$late_penalty;
}
echo $late_penalty;
shell_exec("echo \"$assignment,$deadline1,$deadline2,$ta_contact,$max_marks,$weight,$late_penalty\" >> $assignfile");

header("Location: admin.php");
?>
