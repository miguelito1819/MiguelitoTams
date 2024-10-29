<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>JASAANKNOWN</title>

</head>

<style>
        .header h3 span {
            color: #fbc02d; /* Changing the color of 'Jasaan' to red */
        }

        .header h3 span:last-child {
            color: #d32f2f; /* Changing the color of 'Known' to yellow */
        }
        .container {
        width: 100%; /* Adjust the width as needed */
        height: 500px; /* Adjust the height as needed */
        border: 10px solid #ccc; /* Optional: Add border for visualization */
        padding: 30px; /* Optional: Add padding for spacing */
    }
   
    .sidebar .logo .logo-name{
        margin-left: 10px; /* Add space below the logo */
}
    </style>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="logo">
            <div class="logo-name"><span>Jasaan</span>Known</div>
        </a>
        <ul class="side-menu">
            <li class="active"><a href="dashboard.php" onclick="redirectTo('dashboard.php')"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
            <li class=""><a href="barangay.php" onclick="redirectTo('barangay.php')"><i class='bx bxs-compass'></i>Barangays</a></li>
            <li class=""><a href="maps.php" onclick="redirectTo('maps.php')"><i class='bx bx-map-alt'></i>Maps</a></li>
            <li class=""><a href="users.php" onclick="redirectTo('users.php')"><i class='bx bx-group'></i>Users</a></li>
            <li class=""><a href="settings.php" onclick="redirectTo('settings.php')"><i class='bx bx-cog'></i>Archive</a></li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="#" class="logout">
                    <i class='bx bx-log-out-circle'></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
    <!-- End of Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button class="search-btn" type="submit"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="theme-toggle" hidden>
            <label for="theme-toggle" class="theme-toggle"></label>
            <a href="#" class="notif">
                <i class='bx bx-bell'></i>
                <span class="count"></span>
            </a>
            <a href="#" class="profile">
                <img src="images/logo.png">
            </a>
        </nav>

        <!-- End of Navbar -->

        <main>
    <div class="header">
        <div class="left">
            <h1>About</h1>
        </div>
        <div class="bottom-data">
            <div class="header">
                <h3>WELCOME TO <span>Jasaan</span><span>Known</span></h3>
            </div>
        </div>
        <div class="container">
                   <h4>Description with Background Image</h4>
            </div>

        </div>
    </div>
</main>

            </div>
            <script src="index.js"></script>
</body>

</html>