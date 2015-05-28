<?php

/* $cssFile = "interface.css";
echo " <link rel='stylesheet' href='" . $cssFile . "'></link>";
*/

function connectToServer(){
	$dbhost = 'oniddb.cws.oregonstate.edu';
	$dbname = 'fitzsimk-db';
	$dbuser = 'fitzsimk-db';
	$dbpass = 'VTUimCiHBfyC8P5P';

	$mysqli = new mysqli($dbhost, $dbname, $dbpass, $dbuser);
	if (mysqli_connect_errno()) {
	   echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	return $mysqli;
}



function createTable($mysqli){
	$mysqli->query("CREATE TABLE IF NOT EXISTS users(id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	email  VARCHAR(255) NOT NULL UNIQUE)");
}

function genTable($input){

	$conn = connectToServer();
	$query = "SELECT id, name, category, length, RENTED FROM Movies ORDER by ID";

	if($input != NULL){
		$query ="SELECT id, name,category,length, RENTED FROM Movies WHERE category = '" . $input . "' ORDER by ID";
	}
//	echo "Query: ". $query;

	$result = $conn->query($query);

	echo "<table>";
	echo '<tr>';
	echo "<td> TITLE </td>";
	echo "<td> CATEGORY </td>";
	echo "<td> LENGTH </td>";
	echo "<td> STATUS </td>";
	echo "<td> RENT/RETURN </td>";
	echo "<td></td>";
	echo "</tr>";
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo '<tr>';
			echo "<td class = thead>" . $row["name"]."</td>";
			echo "<td>" . $row["category"]."</td>";
			echo "<td>" . $row["length"]."min </td>";
		echo "<form method='GET'>";			
			if($row["RENTED"] == TRUE){
				echo "<td> checked out </td>";
				echo "<td>";
				echo "<input type='hidden' name='rentStat' value='".$row["RENTED"]."'></input>";
				echo "<input type='hidden' name='rentId' value='".$row["id"]."'></input>";				
				echo "<input type ='submit' class = 'btn' name='submit' action = 'interface.php' value='return'></input>";
				echo "</td>";
				echo "</form>";
			}elseif($row["RENTED"] == FALSE){
				echo "<td> avaliable </td>";
				echo "<td>";
				echo "<input type='hidden' name='rentStat' value='".$row["RENTED"]."'></input>";
				echo "<input type='hidden' name='rentId' value='".$row["id"]."'></input>";				
				echo "<input type ='submit' class = 'btn' name='submit' action = 'interface.php'  value='rent'></input>";
				echo "</td>";
				echo "</form>";
			} 
			echo "<td>";
			echo "<form method = 'GET'>";
			echo "<input type='hidden' name='rentId' value='".$row["id"]."'></input>";	
			echo "<input type ='submit' class = 'btn' name='remove' action = 'interface.php'  value='remove'></input>";
			echo "</form>";
			echo "</td>";
			echo "</tr>";
		}
	} else {
		echo "0 results";
	}
	
	echo "</table>";
	if($result->num_rows > 0){
	echo "<form method = 'GET'>";
	echo "<input type='hidden' name='rentId' value='".$row["id"]."'></input>";	
	echo "<input type ='submit' class = 'btn' name='all' action = 'interface.php'  value='Remove All'></input>";
	echo "</form>";
	}
	$conn->close();
}
function deleteAll(){
	
	$conn = connectToServer();
	$query = "SELECT ID FROM Movies ORDER by ID";

//	$conn->close();
//	echo "Query: ". $query;

	$result = $conn->query($query);
	
	if (0 < $result->num_rows){
		while($row = $result->fetch_assoc()){	
			//echo $row["ID"];
			remove($row["ID"]);
		}
	}if($result->num_rows == 0){
		$conn->close();
	}
}
function dropDownMenu(){
	$conn = connectToServer();
	$query = "SELECT DISTINCT category FROM Movies ORDER by ID";
	$result = $conn->query($query);
	echo "<form name= 'dropdown' method = 'POST' >";
	echo "<select name='selected'>";
	$idNum = 0;
	echo "<option value = NULL >ALL MOVIES</option>";
	while($row = $result->fetch_array()) {
		echo "<option value='" . $row[0] . "' id = '" . $idNum . "'>" . $row[0] . "</option>";
		$idNum++;
	}
	echo "</select>";
	echo "<input type = 'submit' class='btn' value = 'filter'></input>";
	echo "</form>";
}


function remove($id){
	
	$conn = connectToServer();
	if($conn){

		if (!($stmt = $conn->prepare("DELETE FROM Movies WHERE id = ?" ))) {
			echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
		}
		if (!$stmt->bind_param("i",$id)) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
		if (!$stmt->execute()) {
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			} 
		$conn->close();
	}
	else{
		echo "connection failed";
	} 
}


function displayAddForm(){
	  echo "<form id='addVideo' method='POST'>";
	  echo  " Movie <input type = 'text' name = 'name'>";
	  echo " Category <input type = 'text' name = 'category'>";
	  echo  " Length <input type = 'number' name = 'length'>";
	  echo  "<input type = 'submit' name='addBtn' class = 'btn' action = 'interface.php' value='add'>";
	  echo "</form>";
	}

function addUser($email,$username,$password){
	$mysqli = connectToServer();
	$rented = true;
		if (!($stmt = $mysqli->prepare("INSERT INTO users(username,password,email) VALUES (?,?,?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("sss", $username,$password,$email)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		} 
}

function updateDB($id,$bool){

	$conn = connectToServer();
	if($conn){
	if($bool == 1){
		if (!($stmt = $conn->prepare("UPDATE Movies SET RENTED = 0 WHERE id = ?" ))) {
			echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
		}
	}else{
		if (!($stmt = $conn->prepare("UPDATE Movies SET RENTED = 1 WHERE id = ?" ))) {
			echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
		}
	}

	if (!$stmt->bind_param("i",$id)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
	if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		} 
	$conn->close();
	}else{
		echo "connection failed";
	} 
}                                                     
?>
<!-- started at 12:38 -->
