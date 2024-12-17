<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Retrieve user information from session
$fullname = $_SESSION['fullname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egg Incubation Dashboard</title>
    <link rel="icon" href="images/logoh.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f3e5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        h1,h2  {
            color: #7B1FA2;
            margin: 0;
            text-align: center; /* Center the heading */
        }

        p, .card h2, .card p {
            color: #4A148C;
        }
        
        /* Welcome message */
        .welcome-line {
            font-size: 1.2em;
            color: #4A148C;
            text-align: center;
            margin-bottom: 20px;
        }
        
        /* Button styling */
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .action-btn {
            background: #7B1FA2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }
        .action-btn:hover {
            background: #4A148C;
        }
        
        /* Dashboard grid */
        .dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .card {
            background: #EDE7F6;
            border-radius: 8px;
            padding: 20px;
            flex: 1;
            min-width: 300px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        
        footer {
            text-align: center;
            color: #7B1FA2;
            margin-top: 20px;
            font-size: 0.9em;
        }
        
        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }

        /* Graph container styling */
        .graph-container {
            position: relative;
            margin: 20px 0;
            height: 300px;
            background-color: #FFF;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        /* Calendar styling */
        #calendar {
            font-family: Arial, sans-serif;
            margin-top: 15px;
        }
        #calendar div {
            padding: 5px;
            margin: 5px 0;
        }
        #calendar button {
            border: none;
            background: #7B1FA2;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        #calendar button:hover {
            background: #4A148C;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Egg Incubation Monitoring Dashboard</h1>
            <p class="welcome-line">Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>
        </header>
        

        <!-- Button container for Calendar and Log Out -->
        <div class="button-container">
            <button class="action-btn" id="calendarBtn">Calendar</button>
            <a href="logout.php">
                <button class="action-btn">Log Out</button>
            </a>
        </div>

        <!-- Dashboard content -->
        <div class="dashboard">
            <div class="card">
                <h2>Current Temperature</h2>
                <div class="graph-container">
                    <canvas id="temperatureChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h2>Current Humidity</h2>
                <div class="graph-container">
                    <canvas id="humidityChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h2>Recent Hatchings</h2>
                <p>Latest hatchings data will go here...</p>
            </div>
            <div class="card">
                <h2>Alerts</h2>
                <p>No alerts at this time.</p>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 Egg Incubator. All Rights Reserved.</p>
        </footer>
    </div>

    <!-- Modal structure for the calendar -->
    <div id="calendarModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeCalendar">&times;</span>
            <h2>Calendar</h2>
            <div id="calendar"></div>
        </div>
    </div>

    <script>
        // JavaScript for calendar modal functionality
        document.getElementById("calendarBtn").onclick = function() {
            document.getElementById("calendarModal").style.display = "block";
            generateCalendar(new Date());
        };

        document.getElementById("closeCalendar").onclick = function() {
            document.getElementById("calendarModal").style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target == document.getElementById("calendarModal")) {
                document.getElementById("calendarModal").style.display = "none";
            }
        };

        function generateCalendar(currentDate) {
            const calendarDiv = document.getElementById("calendar");
            calendarDiv.innerHTML = ""; // Clear previous calendar

            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            // Header
            const header = document.createElement("div");
            header.style.display = "flex";
            header.style.justifyContent = "space-between";

            const prevBtn = document.createElement("button");
            prevBtn.textContent = "<";
            prevBtn.onclick = () => generateCalendar(new Date(year, month - 1, 1));

            const nextBtn = document.createElement("button");
            nextBtn.textContent = ">";
            nextBtn.onclick = () => generateCalendar(new Date(year, month + 1, 1));

            const title = document.createElement("span");
            title.textContent = `${monthNames[month]} ${year}`;
            title.style.fontWeight = "bold";

            header.append(prevBtn, title, nextBtn);
            calendarDiv.appendChild(header);

            // Days
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            const grid = document.createElement("div");
            grid.style.display = "grid";
            grid.style.gridTemplateColumns = "repeat(7, 1fr)";
            
            for (let i = 0; i < firstDay; i++) grid.appendChild(document.createElement("div"));
            for (let day = 1; day <= daysInMonth; day++) {
                const dayCell = document.createElement("div");
                dayCell.textContent = day;
                dayCell.onclick = () => alert(`Selected: ${day} ${monthNames[month]} ${year}`);
                grid.appendChild(dayCell);
            }

            calendarDiv.appendChild(grid);
        }

        // Temperature Chart
        const tempCtx = document.getElementById("temperatureChart").getContext("2d");
        new Chart(tempCtx, {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                datasets: [{
                    label: "Temperature (°C)",
                    data: [25, 26, 24, 25, 27, 26],
                    borderColor: "rgba(123, 31, 162, 1)",
                    backgroundColor: "rgba(123, 31, 162, 0.2)"
                }]
            },
            options: {
                responsive: true
            }
        });

        // Humidity Chart
        const humCtx = document.getElementById("humidityChart").getContext("2d");
        new Chart(humCtx, {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                datasets: [{
                    label: "Humidity (%)",
                    data: [70, 75, 80, 85, 90, 95],
                    borderColor: "rgba(74, 20, 140, 1)",
                    backgroundColor: "rgba(74, 20, 140, 0.6)"
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>
