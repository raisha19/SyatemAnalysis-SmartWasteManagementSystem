let taskId = parseInt(localStorage.getItem("taskId")) || 1;

// Area -> Users mapping
const areaUsers = {
  "Mohakhali": ["Building A", "Building B", "Resident X"],
  "Banani": ["Building C", "Building D", "Resident Y"],
  "Gulshan": ["Company HQ", "Building E", "Resident Z"],
  "Uttara": ["Tower F", "House G", "Resident H"]
};

// Populate user list based on area
if (document.getElementById("area")) {
  document.getElementById("area").addEventListener("change", function() {
    const selectedArea = this.value;
    const userSelect = document.getElementById("user");
    userSelect.innerHTML = `<option value="">-- Select User --</option>`;
    if (areaUsers[selectedArea]) {
      areaUsers[selectedArea].forEach(user => {
        let opt = document.createElement("option");
        opt.value = user;
        opt.textContent = user;
        userSelect.appendChild(opt);
      });
    }
  });
}

// Handle form submission
if (document.getElementById("scheduleForm")) {
  document.getElementById("scheduleForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const area = document.getElementById("area").value;
    const user = document.getElementById("user").value;
    const address = document.getElementById("address").value;
    const date = document.getElementById("date").value;
    const time = document.getElementById("time").value;
    const collector = document.getElementById("collector").value;

    if (area && user && address && date && time && collector) {
      const newTask = {
        id: "TASK-" + taskId++,
        area,
        user,
        address,
        date,
        time,
        collector,
        status: "Scheduled"
      };

      // Save in localStorage
      let tasks = JSON.parse(localStorage.getItem("tasks")) || [];
      tasks.push(newTask);
      localStorage.setItem("tasks", JSON.stringify(tasks));
      localStorage.setItem("taskId", taskId);

      alert("Task Scheduled Successfully!");
      document.getElementById("scheduleForm").reset();
    } else {
      alert("Please fill all fields!");
    }
  });
}

// Show tasks in table
if (document.getElementById("taskTable")) {
  let tasks = JSON.parse(localStorage.getItem("tasks")) || [];
  const table = document.getElementById("taskTable");

  tasks.forEach((task, index) => {
    const row = table.insertRow();
    row.innerHTML = `
      <td>${task.id}</td>
      <td>${task.area}</td>
      <td>${task.user}</td>
      <td>${task.address}</td>
      <td>${task.date}</td>
      <td>${task.time}</td>
      <td>${task.collector}</td>
      <td>
        <select onchange="updateStatus(${index}, this.value)">
          <option value="Scheduled" ${task.status==="Scheduled"?"selected":""}>Scheduled</option>
          <option value="In Progress" ${task.status==="In Progress"?"selected":""}>In Progress</option>
          <option value="Completed" ${task.status==="Completed"?"selected":""}>Completed</option>
        </select>
      </td>
      <td><button onclick="deleteTask(${index})">Delete</button></td>
    `;
  });
}

// Update status
function updateStatus(index, newStatus) {
  let tasks = JSON.parse(localStorage.getItem("tasks")) || [];
  tasks[index].status = newStatus;
  localStorage.setItem("tasks", JSON.stringify(tasks));
}

// Delete task
function deleteTask(index) {
  let tasks = JSON.parse(localStorage.getItem("tasks")) || [];
  tasks.splice(index, 1);
  localStorage.setItem("tasks", JSON.stringify(tasks));
  location.reload();
}
