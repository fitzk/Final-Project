$(document).ready(function() {
   sessionStorage.clear();
   // Get saved data from sessionStorage
   $.ajax({
      url: "main.php",
      type: "POST",
      async: true,
      data: {
         type: "checkSess"
      }
   }).done(function(data) {
      if (data === "not set") {
         window.location =
            "http://web.engr.oregonstate.edu/~fitzsimk/Final-Project/login.html";
      } else {
         $("#userInfo").append("<p>Welcome " + data +
            " </p>");
      }
   });
   var mtask = document.getElementById('mtask');
   var ntask = document.getElementById('ntask');
   var task = document.getElementById('task');
   var alltask = document.getElementById('alltask');
   var ctask = document.getElementById('ctask');
   var stask = document.getElementById('stask');
   mtask.addEventListener("click", main);
   ntask.addEventListener("click", taskForm);
   task.addEventListener("click", getTasks);
   alltask.addEventListener("click", getAllTasks);
   ctask.addEventListener("click", getCourses);
   stask.addEventListener("click", getAllUserTasks);
   var div = document.createElement('div');
   div.id = 'activeContent';
   var div2 = document.createElement('div');
   div2.id = 'taskF';
   var taskSec = document.getElementById('tasks');
   taskSec.appendChild(div2);
   taskSec.appendChild(div);
   main();
});
function main(){
   $('#tasks').empty();
   var tasks = document.getElementById('tasks');
      var header = document.createElement('header');
   header.appendChild(document.createTextNode(
      "UTime"));
   tasks.appendChild(header);
   var p = document.createElement('p');
 var pText = document.createTextNode(
	"Assignment and studies tracker for University Students.");
 p.appendChild(pText);
 tasks.appendChild(p);
 tasks.appendChild(document.createElement('br'));
 
 var p1 = document.createElement('p');

  var text1 = "The purpose of this site is to provide you, the student, with better information about "+
  " how much time you spend on your courses. Features include a log of Active Tasks, study session timer, "+
  "a graph that shows you how you divide your time between your courses, and access to information about how other students in your"+
  "courses are spending their time on studying and assignments."+" Add an assignment by selecting New Task."+
  " You can then view your Active Tasks and your current progress by selecting Tasks in the navigation list to the left."+
  " Start the timer by pressing the (+) button in the continue column of the table and let the timer run in the background as you study."+
	" When you are done with a study session, just stop the timer"+
  " and your updated log will be visible in the Active Tasks menu."+
  "If you finish a task or assignment, click the check button in the complete column of the table "+
  "and the assignment will no longer be visible in the Active Tasks menu, but it will be visible in the All Tasks menu. Completed tasks will however, still count towards the net "+
  "time applied to the course.  This is represented by the pie chart in the Breakdown section."+
  " If you accidentally left the timer running, you can delete any log by clicking the button in the edit column." +
  "This site does not support manual entry for any task time spent in order to attempt to preserve accuracy (people are generally poor estimators)."+
  "If other users are enrolled in your courses," + 
  " you can access their Active Tasks and progress by navigating to the Peer Tasks on the side bar.";
	
	p1.textContent=text1;


tasks.appendChild(p1);
}
function getCourses() {
   var identifier = {
      type: "getCourseData"
   };
   $.ajax({
      url: "main.php",
      type: "POST",
      async: true,
      data: identifier
   }).done(function(jsonObj) {
      if (jsonObj === "0 results") {
         var tasks = document.getElementById('main');
         var p = document.createElement('p');
         var pText = document.createTextNode(
            "No current courses!");
         p.appendChild(pText);
         tasks.appendChild(p);
      } else {
         var data = JSON.parse(jsonObj);
		 var values = 0;
		 data.forEach(function(object){
			values += parseInt(object.value);
		 });
		 console.log(values);
		 if(values > 0){ 
			 pieChart(data);
		 }else{
			 $('#tasks').empty();
			 $('#tasks').append("<p>No time logged for courses!</p>");
		 }
      }
   });
}
  //http://jsfiddle.net/ragingsquirrel3/qkHK6/
  //based pie chart off of ^
function pieChart(data) {
   
   $('#tasks').empty();
   var tasks = document.getElementById('tasks');
   var header = document.createElement('header');
   header.appendChild(document.createTextNode(
      "Current Time Distribution"));
   tasks.appendChild(header);
   var w = 400;
   var h = 400;
   var r = h / 2;
   var color = d3.scale.category20c();
   var vis = d3.select('#tasks').append("svg:svg").data([data]).attr(
      "width", w).attr("height", h).classed("outerDiv", true).append(
      "svg:g").attr("transform", "translate(" + r + "," + r +
      ")");
   var pie = d3.layout.pie().value(function(d) {
      return d.value;
   });
   // declare an arc generator function
   var arc = d3.svg.arc().outerRadius(r);
   // select paths, use arc generator to draw
   var arcs = vis.selectAll("g.slice").data(pie).enter().append(
      "svg:g").attr("class", "slice");
   arcs.append("svg:path").attr("fill", function(d, i) {
      return color(i);
   }).attr("d", function(d) {
      // log the result of the arc generator 
      console.log(arc(d));
      return arc(d);
   });
   // add the text
   arcs.append("svg:text").attr("transform", function(d) {
      d.innerRadius = 0;
      d.outerRadius = r;
      return "translate(" + arc.centroid(d) + ")";
   }).attr("text-anchor", "middle").text(function(d, i) {
      return data[i].label;
   });
}

function getAllTasks(){
	   //var email = sessionStorage.getItem('email');
   var identifier = {
         type: "getAllTasks"
      };
      //ajax request
   $.ajax({
      url: "main.php",
      type: "POST",
      async: true,
      data: identifier
   }).done(function(jsonObj) {
      if (jsonObj === "0 results") {
		  $('#tasks').empty();
         var tasks = document.getElementById('tasks');
         var p = document.createElement('p');
         var pText = document.createTextNode(
            "No tasks!");
         p.appendChild(pText);
         tasks.appendChild(p);
      } else {
         makeAllTable(jsonObj);
      }
   });
}
function makeAllTable(jsonObj){
	     //if there are no active tasks
      $('#tasks').empty();
      var tbl = document.getElementById("allTasks");
      var tasks = document.getElementById('tasks');
      if (tbl) tbl.parentNode.removeChild(tbl);
      var header = document.createElement('header');
      header.appendChild(document.createTextNode("All Tasks"));
      tasks.appendChild(header);
      var table = document.createElement('table');
      table.className = "sortable";
      table.id = "allTasks";
      var isData = JSON.parse(jsonObj);
      var thead = table.createTHead();
      var row = thead.insertRow(0);
      row.className = "row0";
      addCell(row, "Task");
      addCell(row, "Course");
      addCell(row, "Est.");
      addCell(row, "Total Time");
      addCell(row, "Date Started");
      addCell(row, "Date Finished");

      var tbody = table.appendChild(document.createElement('tbody'));
     // tbody.id = 'tbodyA';
      isData.forEach(function(object, index) {
         var id = "mainTbl";
         //console.log(id);
         var row = addRow(tbody, id);
         addCell(row, object.task);
         addCell(row, object.course);
         addCell(row, object.estimate);
         var secs = String(object.total);
         secs.trim();
         var time = hTime(secs, "s");
         addCell(row, time);
         var isDay = String(object.startDateTime);
         isDay.trim();
         var sdate = moment(isDay, 'YYYY-MM-DD H:m:s').format(
            'M-D-YYYY');
		addCell(row, sdate);
		 var isfDay = String(object.finishDateTime);
         isfDay.trim();
		 var fdate = moment(isfDay, 'YYYY-MM-DD H:m:s').format(
            'M-D-YYYY');
         if (fdate === "Invalid date") {
            fdate = '-';
         }
         addCell(row, fdate);
      });
      tasks.appendChild(table);
      $("tr td:first-child").addClass("firstTd");
      $("tr td:last-child").addClass("lastTd");
}

function getTasks() {
	
   var identifier = {
         type: "getTasks"
      };
      //ajax request
   $.ajax({
      url: "main.php",
      type: "POST",
      async: true,
      data: identifier
   }).done(function(jsonObj) {
      if (jsonObj ==="0 results") {
		  $("#tasks").empty();
         var tasks = document.getElementById('tasks');
         var p = document.createElement('p');
         var pText = document.createTextNode(
            "No active tasks!");
         p.appendChild(pText);
         tasks.appendChild(p);
      } else {
         makeTable(jsonObj);
      }
   });
}

function getAllUserTasks() {
   var identifier = {
         type: "getAllUserTasks",
      };
      //ajax request
   $.ajax({
      url: "main.php",
      type: "POST",
      async: true,
      data: identifier
   }).done(function(jsonObj) {
      var tasks = document.getElementById('tasks');
      if (jsonObj === "0 results") {
		    $('#tasks').empty();
         var p = document.createElement('p');
         var pText = document.createTextNode(
            "No active tasks!");
         p.appendChild(pText);
         tasks.appendChild(p);
      } else {
         $('#tasks').empty();
         var header = document.createElement('header');
         header.appendChild(document.createTextNode(
            "Active Tasks: All Students"));
         tasks.appendChild(header);
         var data = JSON.parse(jsonObj);
         data.forEach(function(object) {
            makeCourseTable(object.value);
         });
      }
   });
}

function makeCourseTable(data) {
   var tasks = document.getElementById('tasks');
   var table = document.createElement('table');
   table.className = "courseTables";
   var tbody = table.appendChild(document.createElement('tbody'));
   tbody.className = "courseTbody";
   data.forEach(function(object, index) {
      if (index === 0) {
         var header = document.createElement('h1');
         header.appendChild(document.createTextNode(object.course));
         header.className = "courseHeader";
         tasks.appendChild(header);
         var thead = table.createTHead();
         var rw = thead.insertRow(0);
         rw.className = "row0";
         addCell(rw, "Task");
         addCell(rw, "Est.");
         addCell(rw, "Total Time");
         addCell(rw, "Last Active");
      }
      var id = "courseTbl";
      var row = addRow(tbody, id);
      addCell(row, object.task);
      addCell(row, object.estimate);
      var secs = String(object.total);
      secs.trim();
      var time = hTime(secs, "s");
      addCell(row, time);
      var isDay = String(object.date);
      isDay.trim();
      var date = moment(isDay, 'YYYY-MM-DD').format(
         'MM-DD-YYYY');
		if(date==="Invalid date"){
			date = '-';
		}
      addCell(row, date);
   });
   tasks.appendChild(table);
   $("tr td:first-child").addClass("firstTd");
   $("tr td:last-child").addClass("lastTd");
}

function makeTable(jsonObj) {
      //if there are no active tasks
      $('#tasks').empty();
      var tbl = document.getElementById("activeTasks");
      var tasks = document.getElementById('tasks');
      if (tbl) tbl.parentNode.removeChild(tbl);
      var header = document.createElement('header');
      header.appendChild(document.createTextNode("Active Tasks"));
      tasks.appendChild(header);
      var table = document.createElement('table');
      table.className = "sortable";
      table.id = "activeTasks";
      var isData = JSON.parse(jsonObj);
      var thead = table.createTHead();
      var row = thead.insertRow(0);
      row.className = "row0";
      addCell(row, "Task");
      addCell(row, "Course");
      addCell(row, "Est.");
      addCell(row, "Total Time");
      addCell(row, "Last Active");
      addCell(row, "Continue");
      addCell(row, "Complete");
      addCell(row, "Edit");
      addCell(row, "Remove");
      var tbody = table.appendChild(document.createElement('tbody'));
      tbody.id = 'tbodyA';
      isData.forEach(function(object, index) {
         var id = "mainTbl";
         //console.log(id);
         var row = addRow(tbody, id);
         addCell(row, object.task);
         addCell(row, object.course);
         addCell(row, object.estimate);
         var secs = String(object.total);
         secs.trim();
         var time = hTime(secs, "s");
         addCell(row, time);
         var isDay = String(object.date);
         isDay.trim();
         var date = moment(isDay, 'YYYY-MM-DD').format(
            'MM-DD-YYYY');
         if (date === "Invalid date") {
            date = '-';
         }
         addCell(row, date);
         var newCell2 = row.insertCell();
         var newCell3 = row.insertCell();
         var newCell4 = row.insertCell();
         var newCell5 = row.insertCell();
         var btn1 = document.createElement('button');
         btn1.clasName = "tblBtn";
         var btn2 = document.createElement('button');
         btn2.clasName = "tblBtn";
         var btn3 = document.createElement('button');
         btn3.clasName = "tblBtn";
         var btn4 = document.createElement('button');
         btn4.clasName = "tblBtn";
         var icon1 = document.createElement('span');
         icon1.className = "glyphicon glyphicon-plus";
         var icon2 = document.createElement('span');
         icon2.className = "glyphicon glyphicon-ok";
         var icon3 = document.createElement('span');
         icon3.className = "glyphicon glyphicon-remove";
         var icon4 = document.createElement('span');
         icon4.className = "glyphicon glyphicon-wrench";
         btn1.addEventListener("click", function() {
            sessionStorage.setItem('currentTask', object.id);
            $("#tbodyA").hide("fast");
            runTimer();
         });
         btn2.addEventListener("click", function() {
            sessionStorage.setItem('currentTask', object.id);
            finish();
            getTasks();
         });
         btn3.addEventListener("click", function() {
            sessionStorage.setItem('currentTask', object.id);
            getTaskLog();
            $("#tbodyA").hide("fast");
         });
         btn4.addEventListener("click", function() {
            sessionStorage.setItem('currentTask', object.id);
            remove();
         });
         btn1.appendChild(icon1);
         btn2.appendChild(icon2);
         btn4.appendChild(icon3);
         btn3.appendChild(icon4);
         newCell2.appendChild(btn1);
         newCell3.appendChild(btn2);
         newCell5.appendChild(btn4);
         newCell4.appendChild(btn3);
      });
      tasks.appendChild(table);
      $("tr td:first-child").addClass("firstTd");
      $("tr td:last-child").addClass("lastTd");
	  
   }
   /*Adds row to table with a unique ID*/
function addRow(tbody, id) {
      var row = tbody.insertRow();
      row.className = id;
      return row;
   }
   /*Adds new cell to row with unique text*/
function addCell(row, cellText) {
   // Insert a cell in the row
   var newCell = row.insertCell();
   // Append a text node to the cell
   var p = document.createElement('p');
   p.className = "tableP";
   var newText = document.createTextNode(cellText);
   p.appendChild(newText);
   newCell.appendChild(p);
}

function remove() {
   var toRemove = {
         type: "removeAll",
         id: sessionStorage.getItem('currentTask')
      };
      //ajax request
   $.ajax({
      url: "remove.php",
      type: "POST",
      async: true,
      data: toRemove
   }).done(function(data) {
      //on return update task list
      //sessionStorage.setItem('currentTask',"");
   }).fail(function(jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
   });
}

function removeOneLog() {
      var toRemove = {
            type: "removeOneLog",
            id: sessionStorage.getItem('subTask'),
			taskId: sessionStorage.getItem('currentTask')
         };
         //ajax request
      $.ajax({
         url: "remove.php",
         type: "POST",
         async: true,
         data: toRemove
      }).fail(function(jqXHR, textStatus) {
         console.log("Request failed: " + textStatus);
      });
   }
   /*Task Form contians the html form and ajax request triggered
   by the form's submission.
   html form: task description, course name, and est
   project time
   ajax: post type, task description, course name, and est
   project time.
   */
function taskForm() {
      //clear main
      $('#tasks').empty();
      var tasks = document.getElementById("tasks");
      var header = document.createElement("header");
      var htext = document.createTextNode("New Task");
      header.appendChild(htext);
      tasks.appendChild(header);
      //create form
      var tForm = document.createElement('form');
      tForm.id = "newTask";
      tForm.className = "form";
      tForm.method = "POST";
      //input for task description
      var taskDesc = document.createElement('input');
      taskDesc.className = "inputField";
      taskDesc.type = "text";
      taskDesc.name = "task";
      taskDesc.value = "";
      taskDesc.placeholder = "Ex: Final Project";
      //input for course
      var course = document.createElement('input');
      course.className = "inputField";
      course.type = "text";
      course.name = "course";
      course.placeholder = "Ex: CS290";
      course.value = "";
      //input for estimate
      var estimate = document.createElement('input');
      estimate.className = "inputField";
      estimate.type = "text";
      estimate.name = "estimate";
      estimate.value = "";
      estimate.placeholder = "Ex: 5.5hours";
      //button for submit
      var submit = document.createElement('input');
      submit.type = "submit";
      //appends each input to form
      var toAppend = [taskDesc, course, estimate, submit];
      toAppend.forEach(function(element) {
         tForm.appendChild(document.createElement("br"));
         tForm.appendChild(element);
         tForm.appendChild(document.createElement("br"));
      });
      //list.insertBefore(newItem, list.childNodes[0]);
      var ttext = document.createTextNode("Task Description");
      var b1 = document.createElement("br");
      var ctext = document.createTextNode("Course Name");
      var etext = document.createTextNode(
         "Estimated Time To Complete Task");
      tForm.insertBefore(ttext, tForm.childNodes[0]);
      tForm.insertBefore(ctext, tForm.childNodes[4]);
      tForm.insertBefore(etext, tForm.childNodes[8]);
      //append form to section main
      tasks.appendChild(tForm);
      //ajax request with new task form info
      //if successful, prints message, asks
      //user if they would like to start task
      $('#newTask').submit(function(event) {
         event.preventDefault();
         var taskData = $(this).serializeArray();
         //	console.log(taskData);
         var t = taskData[0].value;
         var c = taskData[1].value;
         var est = taskData[2].value;
         var tsk = {
               type: "postTask",
               //      email: sessionStorage.getItem('email'),
               task: t,
               course: c,
               estimate: est
            };
            //ajax request
         $.ajax({
            url: "main.php",
            type: "POST",
            async: true,
            data: tsk
         }).done(function(data) {
            var tasks = document.getElementById('tasks');
			var succ = document.getElementById('success');
			if(succ) succ.parentNode.removeChild(succ);
            var p = document.createElement('p');
			p.id="success";
            p.appendChild(document.createTextNode(
               'Your task '+t+' has been added!'));
            tasks.appendChild(p);
         });
         $(this).closest('form').find(
            "input[type=text], textarea").val("");
      });
   }
   /*Add Task Log*/
function addTaskLog() {
   var tskLog = {
         type: "addLog",
         //   email: sessionStorage.getItem('email'),
         id: sessionStorage.getItem('currentTask')
      };
      //ajax request
   $.ajax({
      url: "main.php",
      type: "POST",
      async: true,
      data: tskLog
   }).fail(function(jqXHR, textStatus) {
      console.log("Request failed: " + textStatus);
   });
}

function finish() {
      var tskLog = {
            type: "finish",
            id: sessionStorage.getItem('currentTask')
         };
         //ajax request
      $.ajax({
         url: "main.php",
         type: "POST",
         async: true,
         data: tskLog
      }).fail(function(jqXHR, textStatus) {
         console.log("Request failed: " + textStatus);
      });
   }
   /*Update Task Log*/
function updateTaskLog() {
      var tskLog = {
            type: "updateLog",
            id: sessionStorage.getItem('currentTask')
         };
         //ajax request
      $.ajax({
         url: "main.php",
         type: "POST",
         async: true,
         data: tskLog
      }).done(function(data) {
         //on return update task list
      });
   }
   /*Get Task Log*/
function getTaskLog() {
   //console.log(taskID);
   var tskLog = {
         type: "getLog",
         id: sessionStorage.getItem('currentTask')
      };
      //ajax request
   $.ajax({
      url: "main.php",
      type: "POST",
      async: true,
      data: tskLog
   }).fail(function(jqXHR, textStatus) {
      console.log("Request failed: " + textStatus);
   }).done(function(data) {
      var isData = JSON.parse(data);
      showLog(isData);
   });
}

function showLog(data) {
      var tasks = document.getElementById("tasks");
      var tbl = document.getElementById("taskLogs");
      if (tbl) tbl.parentNode.removeChild(tbl);
	  var head = document.getElementById("logHead");
	  if(head) head.parentNode.removeChild(head);
      var header = document.createElement('header');
	  header.id=('logHead');
      header.appendChild(document.createTextNode("Log Record"));
      tasks.appendChild(header);
      var table = document.createElement('table');
      table.id = "taskLogs";
      var tbody = table.appendChild(document.createElement('tbody'));
      tbody.id = 'tbodyA';
      var thead = table.createTHead();
      var row = thead.insertRow(0);
      row.id = "rowZero";
      addCell(row, "Log No.");
      addCell(row, "Start");
      addCell(row, "Stop");
      addCell(row, "Remove");
      var inD = 0;
      var id;
      data.forEach(function(objLog) {
         id = "row" + inD + "B";
         var row = addRow(tbody, id);
         inD++;
         addCell(row, inD);
         var isDay = String(objLog.start);
         isDay.trim();
         var date = moment(isDay, 'YYYY-MM-DD HH:mm:ss').format(
            'h:mm:ss MM-DD-YYYY');
         var isDay1 = String(objLog.stop);
         isDay1.trim();
         var date1 = moment(isDay1, 'YYYY-MM-DD HH:mm:ss').format(
            'h:mm:ss MM-DD-YYYY');
         addCell(row, date);
         addCell(row, date1);
         var remove = document.createElement('button');
         var icon3 = document.createElement('span');
         icon3.className = "glyphicon glyphicon-remove";
         remove.addEventListener("click", function() {
          //  console.log(objLog['id']);
            sessionStorage.setItem('subTask', objLog.id);
			sessionStorage.setItem('currentTask', objLog.taskId);
			removeOneLog();
			getTaskLog();
         });
         remove.appendChild(icon3);
         var newCell2 = row.insertCell();
         newCell2.appendChild(remove);
      });
      var r = addRow(tbody, id);
	  var newCell1 = r.insertCell();
      var close = document.createElement("button");
	  close.appendChild(document.createTextNode("CLOSE"));
	  close.addEventListener("click", function(){
		  var tbl = document.getElementById("taskLogs");
		  if (tbl) tbl.parentNode.removeChild(tbl);
		  var head = document.getElementById("logHead");
		  if(head) head.parentNode.removeChild(head);
		  $("#tbodyA").show("slow");
		    getTasks();
	  });
	  newCell1.appendChild(close);
      tasks.appendChild(table);
   }
   /*Runs Timer got timer update mechansim from
  http://jsfiddle.net/Daniel_Hug/pvk6p/  */
function runTimer() {
   $("#tasks").empty();
   var timerSec = document.getElementById('tasks');
   var header = document.createElement('header');
   header.appendChild(document.createTextNode("Task Timer"));
   timerSec.appendChild(header);
   var time = document.createElement('time');
   time.id = 'timerFace';
   time.textContent = "00:00:00";
   var seconds = 0,
      minutes = 0,
      hours = 0;
   var t;
   var start = document.createElement('button'),
      stop = document.createElement('button');
   
   start.id = "start";
   start.className = "timerButtons";
   stop.className = "timerButtons";
  
   var startText = document.createTextNode("START"),
      stopText = document.createTextNode("STOP");
   start.appendChild(startText);
   stop.appendChild(stopText);
   var space = document.createElement('br');
   timerSec.appendChild(time);
   timerSec.appendChild(space);
   timerSec.appendChild(start);
   timerSec.appendChild(stop);


   function add() {
      seconds++;
      if (seconds >= 60) {
         seconds = 0;
         minutes++;
         if (minutes >= 60) {
            minutes = 0;
            hours++;
         }
      }
      time.textContent = (hours ? (hours > 9 ? hours : "0" +
         hours) : "00") + ":" + (minutes ? (minutes > 9 ?
         minutes : "0" + minutes) : "00") + ":" + (seconds >
         9 ? seconds : "0" + seconds);
      timer();
   }

   function timer() {
         t = setTimeout(add, 1000);
      }
      /* Start button */
   start.onclick = timer;
   start.addEventListener("click", function() {
      addTaskLog();
      $("aside").animate({
         width: 'toggle'
      });
      start.disabled = true;
   });
   /* Stop button */
   stop.onclick = function() {
      clearTimeout(t);
   };
   stop.addEventListener("click", function() {
      updateTaskLog();
	  getTasks();
      $("#timer").empty();
      $("aside").animate({
         width: 'toggle'
      });
   });
}

function updateTotalTime() {
   var tskLog = {
         type: "updateTime",
         //   email: sessionStorage.getItem('email'),
         id: sessionStorage.getItem('currentTask')
      };
      //ajax request
   $.ajax({
      url: "main.php",
      type: "POST",
      async: true,
      data: tskLog
   }).fail(function(jqXHR, textStatus) {
      console.log("Request failed: " + textStatus);
   });
}

function toSeconds(time1) {
      var hours = parseInt(time1[0]);
      var minutes = parseInt(time1[1]);
      var seconds = parseInt(time1[2]);
      var totalSeconds = hours * 60 * 60 + minutes * 60 + seconds;
      return totalSeconds;
   }
   //function from
   //http://stackoverflow.com/questions/6312993/javascript-seconds-to-time-string-with-format-hhmmss
function hTime(input, units) {
   // units is a string with possible values of y, M, w, d, h, m, s, ms
   var duration = moment().startOf('day').add(input, units),
      format = "";
   if (duration.hour() > 0) {
      format += "h[h] ";
   }
   if (duration.minute() > 0) {
      format += "m[m] ";
   }
   format += " s[s]";
   return duration.format(format);
}