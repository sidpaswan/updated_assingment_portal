<?php
include "phpConfig.php";
session_start();
if(!isset($_SESSION['user'])) {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
function read_file($file_path){
	$handle = fopen($file_path, "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
		echo $line;
	    }
	    fclose($handle);
	}
}
if($_COOKIE['aname']){
	$_SESSION['aname'] = $_COOKIE['aname'];
}
setcookie("aname", "", time()-3600);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Assignment Page</title></head>
<body>
	<div align=center>
	<?php
		if($_SESSION['user']=='admin'){
		$output=shell_exec("$shscripts/gen_option.sh admin");
		}else{
		$username = $_SESSION['user'];
		$output=shell_exec("$shscripts/gen_option.sh $username");	
		}
		echo $output;
	?>
	<br><br>
	</div>
	<?php 
	if($_SESSION['user'] == 'admin'){
	echo "<table align=center border=1 width=50%>";	
		if(is_dir($studentdir)){
			if($od=opendir($studentdir)){
				while(($file=readdir($od))!==false){
					if($file == '..' || $file =='.'){
						continue;
					}
					$new_loc = $studentdir.$file;
					if(is_dir($new_loc)){
						echo "<tr><td><form action='viewStudAsign.php' method='post'><input name='location' type='hidden' value='".$new_loc."'/><input name='sub' value='".$file."' type='submit'></form></td>";
						if($od2=opendir($new_loc)){
							echo "<td>";
							$found=false;
							while(($f2=readdir($od2))!==false){
								if($f2 == $_SESSION['aname'] || $f2 == 'late_'.$_SESSION['aname']){
									echo $f2;
									$found=true;
								}
							}
							if(!$found)
								echo 'Not Submitted';
							echo "</td>";
							closedir($od2);
						}
						$marks_file= $studentdir.$file.'/log/'.$_SESSION['aname'].'.log';
						$sub=file_exists($marks_file);
						echo '<td>';
						if($sub){
							read_file($marks_file);
						}
						else
							echo 'Not Submitted';
						echo '</td>';
						echo '</tr>';
					}
				}
				closedir($od);
			}
		}
	echo "</table>";
	}
	?>
	<div align=center>
	<textarea readonly cols=100 rows=20><?php
	if($_SESSION['user']!='admin'){
		$file_path=$studentdir.'/'.$_SESSION['user'].'/'.$_SESSION['aname'];
		read_file($file_path);
	}else if(isset($_POST['sub'])){
		$file_path = $_POST['location'].'/'.$_SESSION['aname'];
		$late_file_path = $_POST['location'].'/late_'.$_SESSION['aname'];
		$_SESSION['path'] = $_POST['location'];
		if(file_exists($late_file_path))
			read_file($late_file_path);
		else
			read_file($file_path);
	}
	?>
	</textarea>
	</div>
	<div align=center id="marks">
	<?php
		if(isset($_POST['sub']) && $_SESSION['user']=='admin'){
			$roll=$_POST['sub'];
			$fx=$_SESSION['aname'];
			$op = shell_exec("tac $assignfile | grep -F -m 1 $fx");
			$max_marks=explode(',',$op)[4];
			echo 'Marks for '.$roll;
			echo '<form method="POST" action="viewStudAsign.php">';
			echo '<input type="number" name="marks" max="'.$max_marks.'"/> / '.$max_marks.'<br><br>';
			echo '<input type="hidden" value="'.$max_marks.'" name="max_marks">';
			echo '<input type="submit" value="update marks" name="mar"/>';
			echo '</form>';
		}
		if(isset($_POST['mar']) && $_SESSION['user']=='admin'){
			$newfile=$_SESSION['path'].'/log/'.$_SESSION['aname'].'.log';
			$myfile=fopen($newfile,'w') or die ('Could not create updated marks portal');
			if(file_exists($_SESSION['path'].'/late_'.$_SESSION['aname'])){
				$fx=$_SESSION['aname'];
				$op = shell_exec("tac $assignfile | grep -F -m 1 $fx");
				$penalty=explode(',',$op)[6];
				$_POST['marks'] = $_POST['marks']-$penalty;
				echo $_POST['marks'];
			}
			$txt = $_POST['marks'].'/'.$_POST['max_marks'].PHP_EOL;
			fwrite($myfile,$txt);
			fclose($myfile);
			unset($_SESSION['path']);
			header('Location: viewStudAsign.php');
		
		}
	?>
	</div>
</body>
</html>
