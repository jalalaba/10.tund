<?php
class User{
	
	//private - klassi sees
	private $connection;
	//klassi loomisel (new user)
	function __construct($mysqli){
		//this tähendab selle classi muutujat
		$this->connection = $mysqli;
	}
	function createUser($create_email,$hash,$fname,$lname,$age,$city){
		//teen objekti
		//seal on error,->id ja->message
		//või success ja sellel on ->message
		$response=new stdclass();
		//kas selline email on juba olemas
		$stmt=$this->connection->prepare("SELECT id FROM users WHERE email=?");
		$stmt->bind_param("s",$create_email);
		$stmt->bind_result($id);
		$stmt->execute();
		
		//kas sain rea anmdeid
		if($stmt->fetch()){
			//annan errori,selline email on olemas
			$error=new stdclass();
			$error->id=0;
			$error->message="Sellise e-postiga kasutaja on juba olemas!";
			
			$response->error=$error;
			//kõik mis on pärast returni enam ei käivtata
			return $response;
		}
		//panen eelmise päringu kinni
		$stmt->close();
		
		// salvestame andmebaasi

		$stmt = $this->connection->prepare("INSERT INTO users(email,password,first_name,last_name,age,city) VALUES (?,?,?,?,?,?)");
		echo $this->connection->error;
 		echo $stmt->error;
		//asendame ? märgid, ss - s on string email, s on string password,i on integer
		$stmt->bind_param("ssssis",$create_email,$hash,$fname,$lname,$age,$city);
		if($stmt->execute()){
			$success=new stdclass();
			$success->message="Kasutaja edukalt loodud";
			
			$response->success=$success;
			
		}else{
			//midagi läks katki
			$error=new stdclass();
			$error->id=1;
			$error->message="Midagi läks katki";
			
			$response->error=$error;
			//kõik mis on pärast returni enam ei käivtata
			
		}
		$stmt->close();	
		
		return $response;
	}
	
	function loginUser($email,$hash){
		$response=new stdclass();
		//kas selline email on juba olemas
		$stmt=$this->connection->prepare("SELECT id FROM users WHERE email=?");
		$stmt->bind_param("s",$email);
		$stmt->bind_result($id);
		$stmt->execute();
		
		//kas sain rea anmdeid
		if(!$stmt->fetch()){
			$error=new stdclass();
			$error->id=1;
			$error->message="Sellist kasutajat pole";
			
			$response->error=$error;
			return $response;
		}
			$stmt->close();
		
		$stmt = $this->connection->prepare("SELECT id,email FROM users WHERE email=? AND password=? ");
		$stmt->bind_param("ss",$email,$hash);
				
				//muutujuad tulemustele
		$stmt->bind_result($id_from_db, $email_from_db);
		$stmt->execute();
				
				//kontrollin kas tulemusi leiti
		if($stmt->fetch()){
					//ab's oli midagi
			$success=new stdclass();
			$success->message="Kasutaja edukalt sisselogitud";
			
			$response->success=$success;
			
			$user=new stdclass();
			$user->id=$id_from_db;
			$user->email=$email_from_db;
			$response->user=$user;
			
		}else{
			//midagi läks katki
			$error=new stdclass();
			$error->id=1;
			$error->message="Vale parool";
			
			$response->error=$error;
			
		}
		$stmt->close();
		return $response;

	}	

} ?>