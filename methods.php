<?php

function connectToServer()
  {
    $dbhost = 'oniddb.cws.oregonstate.edu';
    $dbname = 'fitzsimk-db';
    $dbuser = 'fitzsimk-db';
    $dbpass = 'VTUimCiHBfyC8P5P';
    $mysqli = new mysqli($dbhost, $dbname, $dbpass, $dbuser);
    if (mysqli_connect_errno())
      {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      }
    return $mysqli;
  }
function createUsersTasks()
  {
    $mysqli  = connectToServer();
    $users   = "CREATE TABLE IF NOT EXISTS users(id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	email  VARCHAR(255) NOT NULL UNIQUE)";
    $tasks   = "CREATE TABLE IF NOT EXISTS tasks(
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	userEmail VARCHAR(255) NOT NULL,
	task VARCHAR(255) NOT NULL,
	course VARCHAR(255) NOT NULL,
	estimate  VARCHAR(255) NOT NULL,
	date DATE DEFAULT '-',
	startDateTime DATETIME NOT NULL,
	finishDateTime DATETIME,
	total INT(50))";
    $taskLog = "CREATE TABLE IF NOT EXISTS taskLog(
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	taskId INT(6) UNSIGNED,
	userEmail VARCHAR(255) NOT NULL,
	start datetime NOT NULL,
	stop datetime)";
    if (!$mysqli->query($users))
      {
        echo "users not created";
      }
    if (!$mysqli->query($tasks))
      {
        echo "tasks not created";
      }
    if (!$mysqli->query($taskLog))
      {
        echo "tasks not created";
      }
    $mysqli->close();
  }


function removeTask($userEmail, $id)
  {
    settype($id, "integer");
    $conn = connectToServer();
    //echo $userEmail;
    //	echo $id;
    if ($conn)
      {
        $stmt = $conn->query("DELETE FROM tasks WHERE userEmail = '" . $userEmail . "' AND  id = '" . $id . "'");
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }
function removeOneLog($userEmail, $id, $tskId)
  {
    settype($id, "integer");
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->query("DELETE FROM taskLog WHERE userEmail = '" . $userEmail . "' AND  id = '" . $id . "'")))
          {
            //echo $stmt;
          }
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
	handleTime($userEmail, $tskId);
  }
function removeLogs($userEmail, $id)
  {
    settype($id, "integer");
    $conn = connectToServer();
    if ($conn)
      {
        $stmt = $conn->query("DELETE FROM taskLog WHERE userEmail = '" . $userEmail . "' AND  taskId = '" . $id . "'");
        $conn->close();
      }
    else
      {
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
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT id, task, course, estimate, startDateTime, finishDateTime, total, date FROM tasks WHERE userEmail= ?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("s", $email))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
          {
            $myArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC))
              {
                if ($row['finishDateTime'] === NULL)
                    $myArray[] = $row;
              }
            $result->close();
            return json_encode($myArray);
          }
        else
          {
            return "0 results";
          }
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }

function getAllTasks($email)
  {
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT id, task, course, estimate, startDateTime, finishDateTime, total, date FROM tasks WHERE userEmail= ?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("s", $email))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
          {
            $myArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC))
              {
                    $myArray[] = $row;
              }
            $result->close();
            return json_encode($myArray);
          }
        else
          {
            return "0 results";
          }
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }  
  
function getAll($course)
  {
    //echo $course;
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT id, userEmail, task, course, estimate, startDateTime, finishDateTime, total, date FROM tasks WHERE course = ?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("s", $course))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
          {
            $myArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC))
              {
                if ($row['finishDateTime'] === NULL)
                  {
                    $myArray[] = $row;
                  }
              }
            $result->close();
            return $myArray;
          }
        else
          {
            return "0 results";
          }
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }
function addTask($userEmail, $task, $course, $estimate)
  {
    $mysqli = connectToServer();
    $total  = "0 hours";
    if (!($stmt = $mysqli->prepare("INSERT INTO tasks(userEmail,task,course, estimate,startDateTime,total) VALUES (?,?,?,?,NOW(),?)")))
      {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
      }
    if (!$stmt->bind_param("sssss", $userEmail, $task, $course, $estimate, $total))
      {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
      }
    if (!$stmt->execute())
      {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
      }
    $mysqli->close();
  }
function addUser($email, $username, $password)
  {
    $mysqli = connectToServer();
    if (!($stmt = $mysqli->prepare("INSERT INTO users(username,password,email) VALUES (?,?,?)")))
      {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
      }
    if (!$stmt->bind_param("sss", $username, $password, $email))
      {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
      }
    if (!$stmt->execute())
      {
        return 1;
      }
	  $mysqli->close();
	  return 0;
  }
function getCourses($email)
  {
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT DISTINCT course FROM tasks WHERE userEmail= ?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("s", $email))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
          {
            $myArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC))
              {
                $myArray[] = $row;
              }
            return json_encode($myArray);
          }
        else
          {
            return "0 results";
          }
        $result->close();
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }
function findUser($username, $password)
  {
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username= ? AND password=?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("ss", $username, $password))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        return $row;
        $result->close();
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }
function getAllStudents($email)
  {
    $courses = getCourses($email);
    $myArray = array();
    foreach ($courses as $course) //foreach element in $arr
      {
        $myArray[] = taskAndTotal($course);
      }
    return json_encode($myArray);
  }
function addLog($email, $id)
  {
    $mysqli = connectToServer();
    settype($id, "integer");
    if ($mysqli)
      {
        $stop = "0000-0-0 00:00:00";
        if (!($stmt = $mysqli->prepare("INSERT INTO taskLog(taskId, userEmail,start,stop) VALUES (?,?, NOW(),?)")))
          {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
          }
        if (!$stmt->bind_param("iss", $id, $email, $stop))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $mysqli->close();
      }
    else
      {
        echo "connection failed";
      }
  }
function updateLog($email, $id)
  {
    settype($id, "integer");
    $mysqli = connectToServer();
    if ($mysqli)
      {
        $stop = "0000-0-0 00:00:00";
        if (!($stmt = $mysqli->prepare("UPDATE taskLog SET stop = NOW() WHERE userEmail = ? AND taskId = ? AND stop = ?")))
          {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
          }
        if (!$stmt->bind_param("sis", $email, $id, $stop))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $mysqli->close();
		handleTime($email, $id);
      }
    else
      {
        echo "connection failed";
      }
    
  }
function finish($email, $id)
  {
    settype($id, "integer");
    $mysqli = connectToServer();
    if ($mysqli)
      {
        if (!($stmt = $mysqli->prepare("UPDATE tasks SET finishDateTime = NOW() WHERE userEmail = ? AND id = ?")))
          {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
          }
        if (!$stmt->bind_param("si", $email, $id))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $mysqli->close();
      }
    else
      {
        echo "connection failed";
      }
  }
function updateTime($email, $id, $total)
  {
    settype($id, "integer");
    settype($total, "integer");
    $mysqli = connectToServer();
    if ($mysqli)
      {
        if (!($stmt = $mysqli->prepare("UPDATE tasks SET total = '$total', date = CURDATE() WHERE userEmail = ? AND id=?")))
          {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
          }
        if (!$stmt->bind_param("si", $email, $id))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $mysqli->close();
      }
    else
      {
        echo "connection failed";
      }
  }
function numTasksCourse($email, $course)
  {
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT id, total FROM tasks WHERE userEmail = ? AND course = ?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("ss", $email, $course))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result  = $stmt->get_result();
        $numRows = mysqli_num_rows($result);
        return $numRows;
        $result->close();
        $conn->close();
      }
    else
      {
        return "0 results";
      }
  }
function numAllTasks($email)
  {
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT id, total FROM tasks WHERE userEmail = ?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("s", $email))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result  = $stmt->get_result();
        $numRows = mysqli_num_rows($result);
        return $numRows;
        $result->close();
        $conn->close();
      }
    else
      {
        return "0 results";
      }
  }
function totalHoursPerCourse($email, $course)
  {
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT total, finishDateTime FROM tasks WHERE userEmail= ? and course=?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("ss", $email, $course))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result    = $stmt->get_result();
        $totalTime = 0;
        if ($result->num_rows > 0)
          {
            $myArray  = array();
            $retArray = array();
            $strArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC))
              {
                //	if($row['finishDateTime'] != NULL ){
                $int = $row['total'];
                $totalTime += $int;
                //	}
              }
            return $totalTime;
          }
        else
          {
            return "0 results";
          }
        $result->close();
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }
/* function taskAndTotal($email,$course){
$conn = connectToServer();
if ($conn) {

if (!($stmt = $conn->prepare("SELECT task, total FROM tasks WHERE userEmail= ? and course=?"))) {
echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
}
if (!$stmt->bind_param("ss", $email,$course)) {
echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (!$stmt->execute()) {
echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
$result = $stmt->get_result();
$totalTime = 0;
if ($result->num_rows > 0) {
$myArray = array();
$retArray = array();
$strArray = array();
while ($row = $result->fetch_array(MYSQL_ASSOC)) {

$myArray['lable'] = $row['tasks'];
$myArray['value']= $row['total'];
$retArray[]=$myArray;
}
return $retArray;
} else {
return "0 results";
}
$result->close();
$conn->close();
} else {
echo "connection failed";
}



} */
//}
function getCourseData($email)
  {
    //numAllTasks($email);
    $allTasks = numAllTasks($email);
    //var_dump($totalTask);
    $course   = getCourses($email);
    $arr      = json_decode($course, true);
    $myArray  = array();
    $newArray = array();
    $strArr   = array();
	if($arr != []){
    foreach ($arr as $item) //foreach element in $arr
      {
        $isCourse          = $item['course'];
        $newArray['label'] = $isCourse;
        $tFormat           = totalHoursPerCourse($email, $isCourse);
        $newArray['value'] = $tFormat;
        $myArray[]         = $newArray;
        unset($strArr);
      }
	}
    return json_encode($myArray);
  }
function all($email)
  {
    //numAllTasks($email);
    //var_dump($totalTask);
    $course   = getCourses($email);
    //	var_dump($course);
    //	unset($arr);
    $arr      = json_decode($course, true);
    //	var_dump($arr);
    $myArray  = array();
    $newArray = array();
    $strArr   = array();
	if($arr != []){
    foreach ($arr as $item) //foreach element in $arr
      {
        $isCourse          = $item['course'];
        $newArray['label'] = $isCourse;
        $tFormat           = getAll($isCourse);
        $newArray['value'] = $tFormat;
        $myArray[]         = $newArray;
        unset($strArr);
      }
    return json_encode($myArray);
	}else{
		return "0 results";
	}
  }
function getLog($email, $id)
  {
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT id, taskId, start, stop FROM taskLog WHERE userEmail= ? AND taskId = ?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("si", $email, $id))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
          {
            $myArray = array();
            while ($row = $result->fetch_array(MYSQL_ASSOC))
              {
                if ($row['stop'] != "0000-0-0 00:00:00")
                  {
                    $myArray[] = $row;
                  }
              }
            $result->close();
            return json_encode($myArray);
          }
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }
function getTotal($email, $id)
  {
    $conn = connectToServer();
    if ($conn)
      {
        if (!($stmt = $conn->prepare("SELECT start,stop FROM taskLog WHERE userEmail= ? AND taskId = ?")))
          {
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
          }
        if (!$stmt->bind_param("si", $email, $id))
          {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        if (!$stmt->execute())
          {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          }
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
          {
            $myArray = array();
            //  $date    = new DateTime('0000-00-00 00:00:00');
            $total   = 0;
			$hours=0;
			$minutes=0;
			$seconds=0;
            while ($row = $result->fetch_array(MYSQL_ASSOC))
              {
                if ($row['stop'] != "0000-0-0 00:00:00")
                  {
                    $s = subtime($row['stop'], $row['start']);
                    //        echo "S: ".$s."<br/>"; 
                    $strArr;
                    //	echo $s;
                    for ($i = 0; $i < strlen($s); $i++)
                      {
                        if ($s[$i] != ':')
                          {
                            $strArr[] = $s[$i];
                          }
                      }
                    $string  = implode($strArr);
                    // echo "string: ".$string."<br/>"; 
                    $strArr2 = str_split($string, 2);
                    
                    for ($j = 0; $j < count($strArr2); $j++)
                      {
                        $isInt = $strArr2[$j];
                        if ($j == 0)
                            $hours = $isInt;
                        if ($j == 1)
                            $minutes = $isInt;
                        if ($j == 2)
                            $seconds = $isInt;
                      }
					   
                    unset($strArr);
                    unset($strArr2);
                    unset($string);
                  }
				 $total += convertToSeconds($hours, $minutes, $seconds);
              }
			   return $total;
            $result->close();
          }
        else
          {
            return "0 results";
          }
        $conn->close();
      }
    else
      {
        echo "connection failed";
      }
  }
//"Y-m-d H:i:s"
function handleTime($email,$id){
	$total = getTotal($email,$id);
	updateTime($email, $id, $total);
}
function addtime($time1, $time2)
  {
    $stop  = new DateTime($time1);
    $start = new DateTime($time2);
    $diff  = $start->add($stop);
    return $diff->format('%H:%I:%S');
  }
function subtime($time1, $time2)
  {
    $stop  = new DateTime($time1);
    $start = new DateTime($time2);
    $diff  = $start->diff($stop);
    return $diff->format('%H:%I:%S');
  }
function hMs($seconds)
  {
    $hours        = $seconds / 60 / 60;
    $minutes      = settype($timeArray[1], "integer");
    $seconds      = settype($timeArray[2], "integer");
    $totalSeconds = $hours * 60 * 60 + $minutes * 60 + $seconds;
  }
function convertToSeconds($hours, $minutes, $seconds)
  {
    $totalSeconds = ($hours * 60 * 60) + ($minutes * 60) + $seconds;
    // echo "Total Seconds: ". $totalSeconds."<br/>";
    return $totalSeconds;
  }
?>