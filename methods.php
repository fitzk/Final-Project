<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function connectToServer()
{
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

function createUsersTasks()
{
    $mysqli = connectToServer();
    
    $users = "CREATE TABLE IF NOT EXISTS users(id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	email  VARCHAR(255) NOT NULL UNIQUE)";
    
    
    $tasks = "CREATE TABLE IF NOT EXISTS tasks(
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	userEmail VARCHAR(255) NOT NULL,
	task VARCHAR(255) NOT NULL,
	course VARCHAR(255) NOT NULL,
	estimate  VARCHAR(255) NOT NULL,
	date DATE DEFAULT '0000-00-00',
	startDateTime DATETIME NOT NULL,
	finishDateTime DATETIME,
	total DATETIME)";
    
    $taskLog = "CREATE TABLE IF NOT EXISTS taskLog(
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	taskId INT(6) UNSIGNED,
	userEmail VARCHAR(255) NOT NULL,
	start datetime NOT NULL,
	stop datetime)";
    
    
    if (!$mysqli->query($users)) {
        echo "users not created";
    }
    if (!$mysqli->query($tasks)) {
        echo "tasks not created";
    }
    if (!$mysqli->query($taskLog)) {
        echo "tasks not created";
    }
    $mysqli->close();
}


function deleteAll()
{
    
    $conn  = connectToServer();
    $query = "SELECT ID FROM Movies ORDER by ID";
    
    //	$conn->close();
    //	echo "Query: ". $query;
    
    $result = $conn->query($query);
    
    if (0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            //echo $row["ID"];
            remove($row["ID"]);
        }
    }
    if ($result->num_rows == 0) {
        $conn->close();
    }
}
function dropDownMenu()
{
    $conn   = connectToServer();
    $query  = "SELECT DISTINCT category FROM Movies ORDER by ID";
    $result = $conn->query($query);
    echo "<form name= 'dropdown' method = 'POST' >";
    echo "<select name='selected'>";
    $idNum = 0;
    echo "<option value = NULL >ALL MOVIES</option>";
    while ($row = $result->fetch_array()) {
        echo "<option value='" . $row[0] . "' id = '" . $idNum . "'>" . $row[0] . "</option>";
        $idNum++;
    }
    echo "</select>";
    echo "<input type = 'submit' class='btn' value = 'filter'></input>";
    echo "</form>";
}
function removeTask($userEmail, $id)
{
    settype($id, "integer");
    $conn = connectToServer();
    //echo $userEmail;
    //	echo $id;
    if ($conn) {
        $stmt = $conn->query("DELETE FROM tasks WHERE userEmail = '" . $userEmail . "' AND  id = '" . $id . "'");
        /* 		if (!($stmt = $conn->prepare("DELETE FROM taskLog WHERE userEmail = ? AND  taskId = ?" ))) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("sd",$userEmai,$id)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }  */
        //printf("Affected rows (DELETE): %d\n", $stmt->affected_rows);	
        
        $conn->close();
    } else {
        echo "connection failed";
    }
}


function removeLogs($userEmail, $id)
{
    //	settype($id, "integer");
    $conn = connectToServer();
    if ($conn) {
        $stmt = $conn->query("DELETE FROM taskLog WHERE userEmail = '" . $userEmail . "' AND  taskId = '" . $id . "'");
        /* 		if (!($stmt = $conn->prepare("DELETE FROM taskLog WHERE userEmail = ? AND  taskId = ?" ))) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("sd",$userEmai,$id)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }  */
        //printf("Affected rows (DELETE): %d\n", $stmt->affected_rows);	
        $conn->close();
    } else {
        echo "connection failed";
    }
}

function removeAll($userEmail, $id)
{
    
    
    removeLogs($userEmail, $id);
    removeTask($userEmail, $id);
}

function getTasks($email)
{
    
    $conn = connectToServer();
    if ($conn) {
        
        if (!($stmt = $conn->prepare("SELECT id, task, course, estimate, startDateTime, finishDateTime, total, date FROM tasks WHERE userEmail= ?"))) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("s", $email)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $myArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                if ($row['finishDateTime'] === NULL)
                    $myArray[] = $row;
            }
            $result->close();
            return json_encode($myArray);
        } else {
            return "0 results";
        }
        $conn->close();
    } else {
        echo "connection failed";
    }
}

function addTask($userEmail, $task, $course, $estimate)
{
    
    $mysqli = connectToServer();
    $total  = "0 hours";
    if (!($stmt = $mysqli->prepare("INSERT INTO tasks(userEmail,task,course, estimate,startDateTime,total) VALUES (?,?,?,?,NOW(),?)"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->bind_param("sssss", $userEmail, $task, $course, $estimate, $total)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    
    
    $mysqli->close();
}


function addUser($email, $username, $password)
{
    $mysqli = connectToServer();
    if (!($stmt = $mysqli->prepare("INSERT INTO users(username,password,email) VALUES (?,?,?)"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->bind_param("sss", $username, $password, $email)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }
}



function getCourses($email)
{
    
    $conn = connectToServer();
    if ($conn) {
        
        if (!($stmt = $conn->prepare("SELECT DISTINCT course FROM tasks WHERE userEmail= ?"))) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("s", $email)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $myArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                $myArray[] = $row;
            }
            return json_encode($myArray);
        } else {
            return "0 results";
        }
		$result->close();
        $conn->close();
    } else {
        echo "connection failed";
    }
}

function findUser($username, $password)
{
    
    $conn = connectToServer();
    if ($conn) {
        
        if (!($stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username= ? AND password=?"))) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("ss", $username, $password)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();
        $row= $result->fetch_assoc();
        return $row;
		$result->close();
        $conn->close();
    } else {
        echo "connection failed";
    }
}
function addLog($email, $id)
{
    $mysqli = connectToServer();
    settype($id, "integer");
    if ($mysqli) {
        $stop = "0000-0-0 00:00:00";
        if (!($stmt = $mysqli->prepare("INSERT INTO taskLog(taskId, userEmail,start,stop) VALUES (?,?, NOW(),?)"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("iss", $id, $email, $stop)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $mysqli->close();
    } else {
        echo "connection failed";
    }
}
function updateLog($email, $id)
{
    settype($id, "integer");
    $mysqli = connectToServer();
    if ($mysqli) {
        $stop = "0000-0-0 00:00:00";
        if (!($stmt = $mysqli->prepare("UPDATE taskLog SET stop = NOW() WHERE userEmail = ? AND taskId = ? AND stop = ?"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("sis", $email, $id, $stop)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $mysqli->close();
    } else {
        echo "connection failed";
    }
	getTotal($email, $id);
}

function updateTime($email, $id, $total)
{
    
    settype($id, "integer");
    $mysqli = connectToServer();
    if ($mysqli) {
        if (!($stmt = $mysqli->prepare("UPDATE tasks SET total = '$total', date = CURDATE() WHERE userEmail = ? AND id=?"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("si", $email, $id)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $mysqli->close();
    } else {
        echo "connection failed";
    }
    
}
function numTasksCourse($email,$course){
		    $conn = connectToServer();
    if ($conn) {
        
        if (!($stmt = $conn->prepare("SELECT id FROM tasks WHERE userEmail = ? AND course = ?"))) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("ss", $email, $course)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();
		$numRows = mysqli_num_rows($result);
		return $numRows;
		$result->close();
		$conn->close();
        } else {
            return "0 results";
        }
}
function numAllTasks($email){
		    $conn = connectToServer();
    if ($conn) {
        
        if (!($stmt = $conn->prepare("SELECT id FROM tasks WHERE userEmail = ?"))) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("s", $email)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();
		$numRows = mysqli_num_rows($result);
		return $numRows;
		$result->close();
		$conn->close();
        } else {
            return "0 results";
        }

}
function getCourseData($email){
	//numAllTasks($email);
	 $allTasks = numAllTasks($email);
	//var_dump($totalTask);
 	$course = getCourses($email);
	$arr = json_decode($course, true);
	
	$myArray = array();
	$newArray = array();
	foreach($arr as $item) { //foreach element in $arr
    $isCourse = $item['course']; //etc
		$newArray['label'] = $isCourse;
		//$percentage = numTasksCourse($email,$isCourse)/$allTasks; 
		$notPercentage = numTasksCourse($email,$isCourse);
		$newArray['value']= $notPercentage;
		$myArray[] = $newArray;
		
	}
	return json_encode($myArray);
}
function getLog($email, $id)
{
    
    $conn = connectToServer();
    if ($conn) {
        
        if (!($stmt = $conn->prepare("SELECT * FROM taskLog WHERE userEmail= ? AND taskId = ?"))) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("si", $email, $id)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $myArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                if ($row['stop'] != "0000-0-0 00:00:00")
                    $myArray[] = $row;
            }
            $result->close();
            return json_encode($myArray);
        } else {
            return "0 results";
        }
        $conn->close();
    } else {
        echo "connection failed";
    }
}

function getTotal($email, $id)
{
    
    $conn = connectToServer();
    if ($conn) {
        
        if (!($stmt = $conn->prepare("SELECT start,stop FROM taskLog WHERE userEmail= ? AND taskId = ?"))) {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param("si", $email, $id)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $myArray = array();
            $date    = new DateTime('0000-00-00 00:00:00');
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                if ($row['stop'] != "0000-0-0 00:00:00") {
                    $s = subtime($row['stop'], $row['start']);
                    
                    $strArr;
                    //	echo $s;
                    for ($i = 0; $i < strlen($s); $i++) {
                        if ($s[$i] != ':') {
                            $strArr[] = $s[$i];
                        }
                    }
                    //var_dump($strArr);
                    if (count($strArr) == 6) {
                        $interval = "PT" . $strArr[0] . $strArr[1] . "H" . $strArr[2] . $strArr[3] . "M" . $strArr[4] . $strArr[5] . "S";
                    }
                    $date->add(new DateInterval($interval));
                    unset($strArr);
                }
                
            }
            $total = $date->format('0000-00-00 H:i:s');
            updateTime($email, $id, $total);
            
            $result->close();
        } else {
            return "0 results";
        }
        $conn->close();
    } else {
        echo "connection failed";
    }
    
}
//"Y-m-d H:i:s"
function subtime($time1, $time2)
{
    $stop  = new DateTime($time1);
    $start = new DateTime($time2);
    $diff  = $start->diff($stop);
    return $diff->format('%H:%I:%S');
}




function convertToSeconds($time)
{
    
    echo "Time: " . $time;
    $timeArray = explode($time, ':');
    
    //  console.log("timeArray: ", timeArray);
    
    $hours   = settype($timeArray[0], "integer");
    $minutes = settype($timeArray[1], "integer");
    $seconds = settype($timeArray[2], "integer");
    
    $totalSeconds = $hours * 60 * 60 + $minutes * 60 + $seconds;
    
    //  echo "IN convert to Seconds: ",$totalSeconds;
    return $totalSeconds;
}
?>