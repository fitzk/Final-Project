$(document).ready(function() {
  // Get saved data from sessionStorage
  var data = sessionStorage.getItem('username');
  if (!data) {
    window.location = "http://web.engr.oregonstate.edu/~fitzsimk/Final-Project/login.html";
  }
  $("#userInfo").append("<p>Welcome " + data + " </p>");

  var ntask = document.getElementById('ntask');
  var task = document.getElementById('task');
  var ctask = document.getElementById('ctask');

  ntask.addEventListener("click", taskForm);
  task.addEventListener("click", getTasks);
  ctask.addEventListener("click", getCourses);
  //stats.addEventListener("click", getUserStats);

  //addTaskBtn();
  var div = document.createElement('div');
  div.id = 'activeContent';

  var div2 = document.createElement('div');
  div2.id = 'taskF';
  var taskSec = document.getElementById('tasks');
  taskSec.appendChild(div2);
  taskSec.appendChild(div);
});

/* function getCourses() {
  var email = sessionStorage.getItem('email');
  var identifier = {
    type: "getCourses",
    email: email
  }

  //ajax request
  $.ajax({
      url: "main.php",
      method: "POST",
      async: true,
      data: identifier
    })
    .done(function(jsonObj) {
      if (jsonObj === "0 results") {
        var tasks = document.getElementById('main');
        var p = document.createElement('p');
        var pText = document.createTextNode("No current courses!");
        p.appendChild(pText);
        tasks.appendChild(p);
      } else {
        
        console.log(jsonObj);
      }

    });
} */
function getCourses(){
	  var identifier = {
		type: "getCourseData",
		email: sessionStorage.getItem('email')
	}
	  $.ajax({
      url: "main.php",
      method: "POST",
      async: true,
      data: identifier
    })
    .done(function(jsonObj) {
      if (jsonObj === "0 results") {
        var tasks = document.getElementById('main');
        var p = document.createElement('p');
        var pText = document.createTextNode("No current courses!");
        p.appendChild(pText);
        tasks.appendChild(p);
      } else {
        
        var data = JSON.parse(jsonObj);
		pieChart(data);
		console.log(data);
      }

    });	
}

function pieChart(data){
	
	//http://jsfiddle.net/ragingsquirrel3/qkHK6/
$('#tasks').empty();
var tasks = document.getElementById('tasks');
  var header = document.createElement('header');
  header.appendChild(document.createTextNode("Current Time Distribution"));
  tasks.appendChild(header);
var w = 400;
var h = 400;
var r = h/2;

var color = d3.scale.category20c();

/* var data = [{"label":"Category A", "value":20}, 
		          {"label":"Category B", "value":50}, 
		          {"label":"Category C", "value":30}]; */


var vis = d3.select('#tasks')
.append("svg:svg")
.data([data])
.attr("width", w)
.attr("height", h)
.classed("outerDiv", true)
.append("svg:g")
.attr("transform", "translate(" + r + "," + r + ")");

var pie = d3.layout.pie().value(function(d){return d.value;});

// declare an arc generator function
var arc = d3.svg.arc().outerRadius(r);

// select paths, use arc generator to draw
var arcs = vis.selectAll("g.slice").data(pie).enter().append("svg:g").attr("class", "slice");
arcs.append("svg:path")
    .attr("fill", function(d, i){
        return color(i);
    })
    .attr("d", function (d) {
        // log the result of the arc generator to show how cool it is :)
        console.log(arc(d));
        return arc(d);
    });

// add the text
arcs.append("svg:text").attr("transform", function(d){
			d.innerRadius = 0;
			d.outerRadius = r;
    return "translate(" + arc.centroid(d) + ")";}).attr("text-anchor", "end").text( function(d, i) {
    return data[i].label;}
		);
		
}

/* nv.addGraph(function() {
  var chart = nv.models.pieChart()
      .x(function(d) { return d.label })
      .y(function(d) { return d.value })
      .showLabels(true);

    d3.select("#chart svg")
        .datum(exampleData())
        .transition().duration(350)
        .call(chart);

  return chart;
}); */
	
function getTasks() {

  var email = sessionStorage.getItem('email');
  var identifier = {
    type: "getTasks",
    email: email
  }

  //ajax request
  $.ajax({
      url: "main.php",
      method: "POST",
      async: true,
      data: identifier
    })
    .done(function(jsonObj) {
      if (jsonObj === "0 results") {
        var tasks = document.getElementById('activeContent');
        var p = document.createElement('p');
        var pText = document.createTextNode("No active tasks!");
        p.appendChild(pText);
        tasks.appendChild(p);
      } else {
        makeTable(jsonObj)
      }

    });
}


function makeTable(jsonObj) {
  //if there are no active tasks
$('#tasks').empty();
  var tbl = document.getElementById("activeTasks");
  var tasks = document.getElementById('tasks');
  if (tbl) tbl.parentNode.removeChild(tbl);
  $('#tasks').empty();
  var header = document.createElement('header');
  header.appendChild(document.createTextNode("Active Tasks"));
  tasks.appendChild(header);
  var table = document.createElement('table');
  table.className = "sortable";
  table.id = "activeTasks";
  var isData = JSON.parse(jsonObj);
  var thead = table.createTHead();
  var row = thead.insertRow(0);
  row.id = "row0";
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
  tbody.id = 'tbody';

  isData.forEach(function(object, index) {
    var id = "row" + object['id'];
    //console.log(id);
    var row = addRow(tbody, id);
    addCell(row, object['task']);
    addCell(row, object['course']);
    addCell(row, object['estimate']);
    //console.log();
    var totalArr = object['total'].split(' ');
    var date = totalArr[0];
    var timeArr = totalArr[1].split(':');
    var h = timeArr[0] + 'h';
    var m = timeArr[1] + 'm';
    var s = timeArr[2] + 's';
    var total;
    if (date != '0000-00-00') {
      var d = date.split('-');
      if (d[2] != '00') {
        var days = d[2] + 'd';
        total = days + h + m + s;
        if (d[1] != '00') {
          var m = d[1] + 'm';
          total = m + days + h + m + s;
          if (d[0] != '0000') {
            var y = d[0] + 'y';
            total = y + m + days + h + m + s;
          }
        }
      }
    } else {
      total = h + m + s;
    }

    addCell(row, total);
    addCell(row, object['date']);
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
    icon4.className = "glyphicon glyphicon-pencil";


    btn1.addEventListener("click", function() {
      sessionStorage.setItem('currentTask', object['id']);
      $("#tbody").hide("fast");
      runTimer();
    });
    btn2.addEventListener("click", function() {
      sessionStorage.setItem('currentTask', object['id']);
    });
    btn4.addEventListener("click", function() {
      sessionStorage.setItem('currentTask', object['id']);
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
  $("#activeTasks tr td:first-child").addClass("firstTd");
  $("#activeTasks tr td:last-child").addClass("lastTd");

}
/*Adds row to table with a unique ID*/
function addRow(tbody, id) {
  var row = tbody.insertRow();
  row.id = id;
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
    email: sessionStorage.getItem('email'),
    id: sessionStorage.getItem('currentTask')
  }

  //ajax request
  $.ajax({
      url: "remove.php",
      method: "POST",
      async: true,
      data: toRemove
    })
    .done(function(data) {
      getTasks();
      //on return update task list
      //sessionStorage.setItem('currentTask',"");
    }).fail(function(jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
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
  var taskForm = document.createElement('form');
  taskForm.id = "newTask";
  taskForm.class = "form";
  taskForm.method = "POST";
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
  estimate.placeholder = "Ex: 5.5";
  //button for submit
  var submit = document.createElement('input');
  submit.type = "submit";
  //appends each input to form
  var toAppend = [taskDesc, course, estimate, submit];
  toAppend.forEach(function(element) {
    taskForm.appendChild(document.createElement("br"));
    taskForm.appendChild(element);
    taskForm.appendChild(document.createElement("br"));
  });
  //list.insertBefore(newItem, list.childNodes[0]);
  var ttext = document.createTextNode("Task Description");
  var b1 = document.createElement("br")

  var ctext = document.createTextNode("Course Name");
  var etext = document.createTextNode("Estimated Time To Complete Task");

  taskForm.insertBefore(ttext, taskForm.childNodes[0]);
  taskForm.insertBefore(ctext, taskForm.childNodes[4]);
  taskForm.insertBefore(etext, taskForm.childNodes[8]);


  //append form to section main

  tasks.appendChild(taskForm);
  //ajax request with new task form info
  //if successful, prints message, asks
  //user if they would like to start task
  $('#newTask').submit(function(event) {


    event.preventDefault();
    var taskData = $(this).serializeArray();
    //	console.log(taskData);
    var t = taskData[0]['value'];
    var c = taskData[1]['value'];
    var est = taskData[2]['value'];

    var tsk = {
      type: "postTask",
      email: sessionStorage.getItem('email'),
      task: t,
      course: c,
      estimate: est
    }

    //ajax request
    $.ajax({
        url: "main.php",
        method: "POST",
        async: true,
        data: tsk
      })
      .done(function(data) {




        var tasks = document.getElementById('tasks');
        var p = document.createElement('p');
        p.appendChild(document.createTextNode('Your task has been added!'));
        tasks.appendChild(p);


        //on return update task list
      });
    $(this).closest('form').find("input[type=text], textarea").val("");
    //	tasks.removeChild(tasks.childNodes[1]);

  });
}
/*Add Task Log*/
function addTaskLog() {

  var tskLog = {
    type: "addLog",
    email: sessionStorage.getItem('email'),
    id: sessionStorage.getItem('currentTask')
  }

  //ajax request
  $.ajax({
      url: "main.php",
      method: "POST",
      async: true,
      data: tskLog
    })
    .done(function(data) {

      //on return update task list
    });
}
/*Update Task Log*/
function updateTaskLog() {

  var tskLog = {
    type: "updateLog",
    email: sessionStorage.getItem('email'),
    id: sessionStorage.getItem('currentTask')
  }

  //ajax request
  $.ajax({
      url: "main.php",
      method: "POST",
      async: true,
      data: tskLog
    })
    .done(function(data) {
      //on return update task list
      getTasks();
    });
}
/*Get Task Log*/
function getTaskLog(taskID) {

  //console.log(taskID);

  var tskLog = {
    type: "taskLog",
    email: sessionStorage.getItem('email'),
    id: taskID
  }

  //ajax request
  $.ajax({
      url: "main.php",
      method: "POST",
      async: true,
      data: tskLog
    })
    .done(function(data) {

    });
}
function showLog(){
	
	
	
	
}


/*Runs Timer*/
function runTimer() {

  $("#tasks").empty();
  var timerSec = document.getElementById('tasks');
  var header = document.createElement('header');
  header.appendChild(document.createTextNode("Task Tracker"));
  timerSec.appendChild(header);

  var time = document.createElement('time');
  time.id = 'timerFace';
  time.textContent = "00:00:00";
  var seconds = 0,
    minutes = 0,
    hours = 0;
  var t;

  var start = document.createElement('button'),
    stop = document.createElement('button'),
    clear = document.createElement('button');
  start.id = "start";
  start.className = "timerButtons";
  stop.className = "timerButtons";
  clear.className = "timerButtons";
  var startText = document.createTextNode("START"),
    stopText = document.createTextNode("STOP"),
    clearText = document.createTextNode("CLEAR");

  start.appendChild(startText);
  stop.appendChild(stopText);
  clear.appendChild(clearText);
  var space = document.createElement('br');

  timerSec.appendChild(time);
  timerSec.appendChild(space);
  timerSec.appendChild(start);
  timerSec.appendChild(stop);
  timerSec.appendChild(clear);





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

    time.textContent = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);

    timer();
  }

  function timer() {
    t = setTimeout(add, 1000);
  }

  /* Start button */

  start.onclick = timer;

  start.addEventListener("click", function() {
    addTaskLog();
    start.disabled = true;
  });


  /* Stop button */
  stop.onclick = function() {
    clearTimeout(t);
  }
  stop.addEventListener("click", function() {
    updateTaskLog();
    updateTotalTime;
    $("#timer").empty();

  });

  /* Clear button */
  clear.onclick = function() {
      time.textContent = "00:00:00";
      seconds = 0;
      minutes = 0;
      hours = 0;
      generateHomepage();
    }
    //http://jsfiddle.net/Daniel_Hug/pvk6p/

}


function updateTotalTime() {

  var tskLog = {
    type: "updateTime",
    email: sessionStorage.getItem('email'),
    id: sessionStorage.getItem('currentTask')
  }

  //ajax request
  $.ajax({
      url: "main.php",
      method: "POST",
      async: true,
      data: tskLog
    })
    .done(function(data) {

    });

}
