<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities</title>
    <style>
        /* Your CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .activity-container {
            display: flex;
            flex-direction: column; /* Align activities from top to bottom */
            overflow-y: auto; /* Enable vertical scrolling */
            padding: 20px;
            gap: 20px;
        }

        .activity {
            background-color: #333;
            border-radius: 10px;
            overflow: hidden;
            min-width: 700px;
            max-width: 700px;
            display: flex;
            flex-direction: column;
            position: relative; /* Added for positioning comment form */
        }

        .activity img {
            width: 100%;
            height: auto;
        }

        .activity-details {
            padding: 15px;
        }

        .activity-details h2 {
            margin: 0 0 10px;
        }

        .activity-description, .activity-date {
            margin: 5px 0;
        }

        .play-button {
            background-color: #6a0dad;
            color: #fff;
            border: none;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .play-button:hover {
            background-color: #8a2be2;
        }

        .comments-container {
            background-color: #444;
            padding: 10px;
            border-radius: 0 0 10px 10px;
            margin-top: auto; /* Push comments to the bottom */
        }

        .comment {
            color: #ccc;
            font-size: 14px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .comment .profile-pic {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-text {
            flex: 1;
        }

        .comment-form {
            padding: 10px;
            background-color: #444;
            border-radius: 0 0 10px 10px;
            margin-top: auto;
        }

        .comment-form textarea {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 8px;
            border: none;
            border-radius: 5px;
            resize: none;
        }

        .comment-form button {
            background-color: #6a0dad;
            color: #fff;
            border: none;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
        }

        .comment-form button:hover {
            background-color: #8a2be2;
        }
    </style>
</head>
<body>
<div class="header">
                <div class="left">
                    <h1>Activities</h1>
                    <button id="addActivityBtn"><a href="registerA.php" style="text-decoration:none; color:inherit;">Add Activity</a></button>
                    <ul class="breadcrumb">
                        <li><a href="#"></a></li>
                    </ul>
                </div>
            </div>
    <div class="activity-container">
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "jk";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch activities from database
        $sql = "SELECT id, actname, barangay, date, picture, description FROM activities";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo '<div class="activity">';
                echo '<img src="' . $row["picture"] . '" alt="Activity Image">';
                echo '<div class="activity-details">';
                echo '<h2>' . $row["actname"] . '</h2>';
                echo '<p class="activity-barangayname">' . $row["barangay"] . '</p>';
                echo '<p class="activity-description">' . $row["description"] . '</p>';
                echo '<p class="activity-date">' . $row["date"] . '</p>';
                echo '</div>';
                // Add comments container
                echo '<div class="comments-container">';
                // Fetch and display comments for each activity
                $activity_id = $row["id"]; // Assuming 'id' is the column name for the activity ID
                $comments_sql = "SELECT * FROM comments WHERE activity_id = $activity_id";
                $comments_result = $conn->query($comments_sql);
                if ($comments_result->num_rows > 0) {
                    while ($comment_row = $comments_result->fetch_assoc()) {
                        echo '<div class="comment">';
                        echo '<img class="profile-pic" src="images/default-avatar.png" alt="Profile Picture">';
                        echo '<div class="comment-text">' . $comment_row["comment"] . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "No comments yet.";
                }
                echo '</div>';
                // Comment form
                echo '<form class="comment-form" method="post" action="submit_comment.php">';
                echo '<input type="hidden" name="activity_id" value="' . $activity_id . '">';
                echo '<textarea name="comment" placeholder="Add your comment"></textarea>';
                echo '<button type="submit">Submit</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo "0 results";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>





    <script>
        const activities = [
            {
                name: 'Mission: Yozakura Family',
                description: 'High school student Taiyou Asano has been socially inept ever since his family died in a car crash. The only person he can properly interact with is his childhood friend, Mutsumi Yozakura—the head of...',
                date: 'Apr 07, 2024',
                img: /path/to/images/shaiwo.jpg
            },
            // Add more activities here
        ];

        const container = document.querySelector('.activity-container');

        activities.forEach(activity => {
            const activityDiv = document.createElement('div');
            activityDiv.classList.add('activity');

            activityDiv.innerHTML = `
                <img src="${activity.img}" alt="Activity Image">
                <div class="activity-details">
                    <h2>${activity.name}</h2>
                    <p class="activity-description">${activity.description}</p>
                    <p class="activity-date">${activity.date}</p>
                    <button class="play-button">Play Now</button>
                </div>
            `;

            container.appendChild(activityDiv);
        });
    </script>
</body>
</html>
