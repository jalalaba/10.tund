<?php
class InterestsManager{
	
	private $connection;
	private $user_id;
	
	function __construct($mysqli,$user_id_from_session){
		
		$this->connection=$mysqli;
		$this->user_id=$user_id_from_session;
		
		echo "Huvialade haldus käivitatud".$this->user_id;
	
	}
	
	function addInterest($new_interest){
		//võtke eeskuju createuser klassist user
		// 1) kontrollite kas selline huviala on olemas
		// 2) kui ei ole siis lisate juurde
		$response=new stdclass();
		//kas selline interest on juba olemas
		$stmt=$this->connection->prepare("SELECT id FROM interests WHERE name=?");
		$stmt->bind_param("s",$new_interest);
		$stmt->bind_result($id);
		$stmt->execute();
		
		//kas sain rea anmdeid
		if($stmt->fetch()){
			
			$error=new stdclass();
			$error->id=0;
			$error->message="Huviala <strong>".$new_interest."</strong> on juba olemas!";
			
			$response->error=$error;
			//kõik mis on pärast returni enam ei käivtata
			return $response;
		}
		//panen eelmise päringu kinni
		$stmt->close();
		
		$stmt = $this->connection->prepare("INSERT INTO interests(name) VALUES (?)");
		
		//asendame ? märgid, ss - s on string email, s on string password,i on integer
		$stmt->bind_param("s",$new_interest);
		if($stmt->execute()){
			$success=new stdclass();
			$success->message="Huviala edukalt loodud";
			
			$response->success=$success;
			
		}else{
			echo $stmt->error;
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
		
	function createDropdown(){
		
		$html="";
		$html.='<select name="new_dd_selection">';
		
		//$html.='<option>3</option>';
		//EI TÖÖTA KORRALIKULT
		
		$stmt=$this->connection->prepare("select interests.id,interests.name from interests
		LEFT JOIN user_interests ON interests.id=user_interests.interests_id 
		WHERE user_interests.interests_id IS NULL OR user_interests.interests_id !=?");
		$stmt->bind_param("i",$this->user_id);
		$stmt->bind_result($id,$name);
		$stmt->execute();
		while($stmt->fetch()){
			$html.='<option value="'.$id.'">'.$name.'</option>';
		}
		$html.='</select>';
		return $html;
		
	}	
		
	function addUserInterest($new_interest_id){
		// 1)kontrollin ega pole juba olemas
		// 2) lisan juurde
		//user_interests
		//interests_id see mis kasutaja sisestas
		//user_id on muutujas $this->user_id
		$response=new stdclass();
		$stmt=$this->connection->prepare("SELECT id FROM user_interests WHERE user_id=? AND interests_id=?");
		$stmt->bind_param("ii",$this->user_id,$new_interest_id);
		$stmt->bind_result($id);
		$stmt->execute();
		
		//kas sain rea anmdeid
		if($stmt->fetch()){
			//annan errori,selline email on olemas
			$error=new stdclass();
			$error->id=0;
			$error->message="Selline huviala on sinul juba olemas!";
			
			$response->error=$error;
			//kõik mis on pärast returni enam ei käivtata
			return $response;
		}
		//panen eelmise päringu kinni
		$stmt->close();
		
		// salvestame andmebaasi

		$stmt = $this->connection->prepare("INSERT INTO user_interests(user_id,interests_id) VALUES (?,?)");
		echo $this->connection->error;
 		echo $stmt->error;
		//asendame ? märgid, ss - s on string email, s on string password,i on integer
		$stmt->bind_param("ii",$this->user_id,$new_interest_id);
		if($stmt->execute()){
			$success=new stdclass();
			$success->message="huviala edukalt loodud";
			
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
	
	function getUserInterest(){
		$html='';
		$stmt=$this->connection->prepare("SELECT interests.name FROM user_interests INNER JOIN interests ON user_interests.interests_id=interests.id WHERE user_interests.user_id=?");
		$stmt->bind_param("i",$this->user_id);
		$stmt->bind_result($name);
		$stmt->execute();
		while($stmt->fetch()){
			$html.='<p>'.$name.'<p>';
		}
		return $html;
	}
}?>