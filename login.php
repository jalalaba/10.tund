<?php

	// LOGIN.PHP
	require_once("functions.php");
	
	//kui kasutaja on sisseloginud,siis suunan data.php lehele
	if(isset($_SESSION["logged_in_user_id"])){
		header("Location: data.php");
	}
	
	//muutujad errorite jaoks
	$email_error = "";
	$password_error = "";
	$fname_error = "";
	$lname_error = "";
	$create_email_error = "";
	$create_password_error = "";
	$age_error = "";
	$city_error = "";
	
	//muutujad väärtuste jaoks
	$email = "";
	$password = "";
	$fname = "";
	$lname = "";
	$create_email = "";
	$create_passw = "";
	$age = "";
	$city = "";
	$hash = "";
	
	
	// kontrollime et keegi vajutas input nuppu
	if($_SERVER["REQUEST_METHOD"]=="POST"){
		//kontrollin millist nuppu vajutati
		if(isset($_POST["login"])){
		//kontrollin et e-post ei ole tühi
			if (empty($_POST["email"])){
			$email_error = "see väli on kohustulik";	
			} else {
				$email=test_input($_POST["email"]);
			}
			//kontrollin et parool ei ole tühi
			if (empty($_POST["password"])){
			$password_error = "see väli on kohustulik";
			} else {
			
				//kui oleme siia jõudnud, siis parool pole tühi
				if(strlen($_POST["password"]) < 8){
				$password_error="peab olema vähemalt 8 tähemärki";
				} else {
					$password=test_input($_POST["password"]);
				}
			}
			
			// Kui oleme siia jõudnud, võime kasutaja sisse logida
			if($password_error == "" && $email_error == ""){
			
				$hash = hash("sha512", $password);	
				$login_response=$User->loginUser($email,$hash);		
				//kasutaja logis edukalt sisse
				if (isset($login_response->success)){
					//id,email
					$_SESSION["logged_in_user_id"]=$login_response->user->id;
					$_SESSION["logged_in_user_email"]=$login_response->user->email;
					//saadan sõnumi teise faili kasutades sessiooni
					$_SESSION["login_success_message"]=$login_response->success->message;
					header("Location:data.php");
				}
			}	
		}
	
	
		//Kasutaja loomine
		if(isset($_POST["submit"])){
			
		//kontrollin et eesnimi ei ole tühi
			if (empty($_POST["first_name"])){
				$fname_error = "see väli on kohustulik";
			}else{
				$fname=test_input($_POST["first_name"]);
			}
			if (empty($_POST["last_name"])){
				$lname_error = "see väli on kohustulik";
			}else{
				$lname=test_input($_POST["last_name"]);
			}
			if (empty($_POST["create_email"])){
				$create_email_error = " see väli on kohustulik";			
			} else {
				$create_email=test_input($_POST["create_email"]);
			}
			if (empty($_POST["create_password"])){
				$create_password_error = "see väli on kohustulik";	
			} else {			
				if(strlen($_POST["create_password"]) < 8){
					$create_password_error="peab olema vähemalt 8 tähemärki";
				
				} else{
					$create_password = test_input($_POST["create_password"]);
				//kõik korras
				//test_input eemaldab pahatahlikud osad
				}
			}	
			if (empty($_POST["age"])){
				$age_error = " see väli on kohustulik";			
			} else {
					$age = intval($age);
				if($age > 5 && $age <100  ){
					$age_error = "Pane sait kohe kinni, oled interneti kasutamiseks liiga noor või liiga vana";
				}else{
					$age=test_input($_POST["age"]);
					}
			}		
			if (empty($_POST["city"])){
				$city_error = " see väli on kohustulik";			
			} else {
				$city=test_input($_POST["city"]);	
				}
				
			//if($fname_error =="" && $lname_error =="" && $create_email_error =="" && $create_password_error =="" && $age_error ="" && $city_error =""){
				
				//räsi paroolist, mille salvestame andmebaasi
				$hash = hash("sha512",$create_password);
				
				echo "Võib kasutajat luua! Kasutajanimi on ".$create_email." ja parool on ".$create_password." ja räsi on ".$hash.$fname.$lname;
				
				$create_response=$User->createUser($create_email,$hash,$fname,$lname,$age,$city);
				
			//}			
		}
	}
	
	
function test_input($data) {
	//võtab ära tühikud,enterid jne
	$data = trim($data);
	//võtab ära tagurpidi kaldkriipsud
	$data = stripslashes($data);
	//teeb html-i tekstiks
	$data = htmlspecialchars($data);
	return $data;
	}

	

?>
<?php
	$page_title = "Login" ;
	$page_file_name = "login.php";
?>

	<h2>Log in</h2>
	
	<?php if(isset($login_response->error)):?>
		<p><?=$login_response->error->message;?></p>
	<?php elseif(isset($login_response->success)):?>
		<p><?=$login_response->success->message;?></p>
	<?php endif; ?>
	
		<form action="login.php" method="post">
			<input name="email" type="email" placeholder="E-post" value="<?php echo $email; ?>"> <?php echo $email_error; ?><br><br>
			<input name="password" type="password" placeholder="Parool"> <?php echo $password_error; ?> <br><br>
			<input name="login" type="submit" value="log in"> <br><br>
		</form>
		
	<h2>Create user</h2>
	
	<?php if(isset($create_response->error)):?>
		<p><?=$create_response->error->message;?></p>
	<?php elseif(isset($create_response->success)):?>
		<p><?=$create_response->success->message;?></p>
	<?php endif; ?>
	
		<form action="login.php" method="post">
			<input name="create_email" type="email" placeholder="E-post" value="<?php echo $create_email; ?>"><?php echo $create_email_error; ?><br><br>
			<input name="create_password" type="password" placeholder="Parool"> <?php echo $create_password_error; ?> <br><br>
			<input name="first_name" type="text" placeholder="Eesnimi" value="<?php echo $fname; ?>"> <?php echo $fname_error; ?><br><br>
			<input name="last_name" type="text" placeholder="Perekonnanimi" value="<?php echo $lname; ?>"> <?php echo $lname_error; ?><br><br>
			<input name="age" type="number_format" placeholder="Vanus" value="<?php echo $age; ?>"><?php echo $age_error; ?><br><br>
			<input name="city" type="text" placeholder="Linn" value="<?php echo $city; ?>"><?php echo $city_error; ?><br><br>
			<input name="submit" type="submit" value="Submit"><br><br>
		</form>	
	<h2>Minu mvp idee. Lehekülg kus saab hmm...ma ei tea. Kasutajad saavad üles laadida oma joonistusi ja neid müüa. </h2>
	
