<?php
// Include database configuration
include 'config.php';
session_start();

if (isset($_POST['send_response'])) {
    $phone = $_POST['mobile_no'];
    $title = $_POST['title'];
    $message = $_POST['message'];  // Get selected message from form

    $send_data = [];
    $send_data['mobile'] = '+63' . $phone;
    $send_data['message'] = $message;
    $send_data['token'] = 'cf08d16eac5ce663c8f7bed33418b745';  // Example token, replace with the actual token
    $parameters = json_encode($send_data);
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://app.qproxy.xyz/api/sms/v1/send");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "Content-Type: application/json"
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $get_sms_status = curl_exec($ch);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
    $title = $_POST['title'];
    $complaint = $_POST['complaint'];
    $type = $_POST['type'];
    $date = date('Y-m-d');  // Current date
    $barangay = $_POST['barangay'];
    $file = '';

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
        $targetDir = "uploaded_files/";
        $targetFile = $targetDir . basename($_FILES["file"]["name"]);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Allow only specific file types
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $file = $targetFile;
            } else {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Only JPG, JPEG, PNG, PDF, DOC, and DOCX files are allowed.";
            exit();
        }
    }

    // Insert the complaint into the database
    $stmt = $conn->prepare("INSERT INTO complaints (title, complaint, type, date, barangay, file) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $title, $complaint, $type, $date, $barangay, $file);

    if ($stmt->execute()) {
        // Redirect to avoid form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error submitting complaint: " . $stmt->error;
    }
}
// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $complaint_id = $_POST['complaint_id'];

    // Update the complaint status in the database
    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $complaint_id);

    if ($stmt->execute()) {
        // Redirect to avoid form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error updating status: " . $stmt->error;
    }
}

// Fetch existing complaints
$complaints = $conn->query("SELECT * FROM complaints ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .card {
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1rem;
        }

        .card-text {
            font-size: 0.875rem;
        }

        .badge {
            font-size: 0.75rem;
        }

        .form-container {
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    <!-- Complaint List -->
    <div class="col-md-8">
        <div class="row">
            <?php while ($row = $complaints->fetch_assoc()) { ?>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['complaint']); ?></p>
                            <span class="badge badge-info"><?php echo htmlspecialchars($row['type']); ?></span>
                            <span class="badge badge-secondary"><?php echo htmlspecialchars($row['date']); ?></span>
                            <span class="badge badge-success"><?php echo htmlspecialchars($row['barangay']); ?></span>
                            <span class="badge badge-warning"><?php echo htmlspecialchars($row['status']); ?></span>
<!-- Update Status Button -->
<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#updateModal<?php echo $row['id']; ?>" style="background-color: #ffc107; border-radius: 8px; color: white; width: 80px; height: 20px; font-size: 10px;">
    <i class="fas fa-sync"></i> Update Status
</button>

                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal<?php echo $row['id']; ?>" style="background-color: #007bff; border-radius: 8px; color: white; width: 60px; height: 20px; font-size: 10px;">
                                <i class="fas fa-eye"></i> View
                            </button>

                        </div>
                    </div>
                </div>

<!-- Modal for Updating Complaint Status -->
<div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel<?php echo $row['id']; ?>">Update Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                    
                    <!-- Dropdown for status selection -->
                    <div class="form-group">
                        <label for="status">Select Status:</label>
                        <select name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="under observation">Under Observation</option>
                            <option value="accomplished">Resolved</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-success">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>


                <!-- Modal for Viewing Complaint Details -->
                <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel<?php echo $row['id']; ?>">Complaint Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Title:</strong> <?php echo htmlspecialchars($row['title']); ?></p>
                                <p><strong>Complaint:</strong> <?php echo htmlspecialchars($row['complaint']); ?></p>
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></p>
                                <p><strong>Barangay:</strong> <?php echo htmlspecialchars($row['barangay']); ?></p>
                                <p><strong>Complainant:</strong> <?php echo htmlspecialchars($row['complainant']); ?></p>
                                <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($row['contactnum']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                                <?php if ($row['file']) { ?>
                                    <p><strong>File:</strong> <a href="<?php echo $row['file']; ?>" target="_blank">View File</a></p>
                                <?php } ?>
                            </div>
                            <div class="modal-footer">
                                <form action="" method="POST">
                                    <input type="hidden" name="title" value="<?= $row['title'] ?>">
                                    <input type="hidden" name="mobile_no" value="<?= $row['contactnum'] ?>">

                                    <!-- Dropdown for message selection -->
                                    <div class="form-group">
                                        <label for="message">Select Message:</label>
                                        <select name="message" class="form-control" required>
                                            <option value="Good Day! Your complaint for '<?= $row['title'] ?>' is currently under observation. We will update you soon. Thank you for your patience.">Under Observation</option>
                                            <option value="We need additional information regarding your complaint for '<?= $row['title'] ?>'. Kindly contact us for verification. Thank you.">Pending</option>
                                            <option value="Your complaint for '<?= $row['title'] ?>' has been actioned and resolved. Thank you for your cooperation.">Actioned and Resolved</option>
                                        </select>
                                    </div>

                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="send_response" class="btn btn-success">Send Response</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
