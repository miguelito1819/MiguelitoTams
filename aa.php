<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'jk');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate age from birthdate
function calculateAge($bday) {
    $birthDate = new DateTime($bday);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age;
}

// Convert checkbox values into a comma-separated string or NULL
function convertCheckboxValues($checkboxValues) {
    return isset($checkboxValues) ? implode(',', $checkboxValues) : NULL;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch common fields
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $ext = $_POST['ext'];
    $gender = $_POST['gender'];
    $bday = $_POST['bday'];
    $age = calculateAge($bday);
    $religion = $_POST['religion'];
    $civil_status = $_POST['civil_status'];
    $employment_status = $_POST['employment_status'];
    $voter_status = $_POST['voter_status'];
    $educ_attainment = $_POST['educ_attainment'];
    $contact_num = $_POST['contact_num'];
    $is_household_head = isset($_POST['is_household_head']) ? 1 : 0;

    // New fields
    $student_level = isset($_POST['student_level']) ? $_POST['student_level'] : NULL;
    $type_of_work = isset($_POST['type_of_work']) ? $_POST['type_of_work'] : NULL;

    if ($is_household_head) {
        // If household head, save family count, income, and address
        $fam_count = $_POST['fam_count'];
        $fam_income = $_POST['fam_income'];
        $address = $_POST['address'];

        // Collect owned vehicles, appliances, gadgets, and benefits
        $owned_vehicles = convertCheckboxValues($_POST['owned_vehicles']);
        $owned_appliances = convertCheckboxValues($_POST['owned_appliances']);
        $owned_gadgets = convertCheckboxValues($_POST['owned_gadgets']);
        $benefits = convertCheckboxValues($_POST['benefits']);
        
        // House information
        $house_ownership = $_POST['house_ownership']; // 'owned' or 'rental'
        $house_area = $_POST['house_area'];
        $date_built = $_POST['date_built'];

        // Water and electricity ownership
        $own_water = isset($_POST['own_water']) ? 1 : 0;
        $water_source = isset($_POST['water_source']) ? $_POST['water_source'] : NULL;
        $own_electricity = isset($_POST['own_electricity']) ? 1 : 0;

        // Business information
        $has_business = isset($_POST['has_business']) ? 1 : 0;
        $business_type = isset($_POST['business_type']) ? $_POST['business_type'] : NULL;

        // Insert into database
        $sql = "INSERT INTO residents (fname, mname, lname, ext, gender, bday, age, religion, civil_status, employment_status, voter_status, educ_attainment, contact_num, is_household_head, fam_count, fam_income, address, owned_vehicles, owned_appliances, owned_gadgets, benefits, house_ownership, house_area, date_built, own_water, water_source, own_electricity, has_business, business_type, student_level, type_of_work)
                VALUES ('$fname', '$mname', '$lname', '$ext', '$gender', '$bday', '$age', '$religion', '$civil_status', '$employment_status', '$voter_status', '$educ_attainment', '$contact_num', 1, '$fam_count', '$fam_income', '$address', '$owned_vehicles', '$owned_appliances', '$owned_gadgets', '$benefits', '$house_ownership', '$house_area', '$date_built', '$own_water', '$water_source', '$own_electricity', '$has_business', '$business_type', '$student_level', '$type_of_work')";
    } else {
        // For household members, save the head ID and inherit the address
        $household_head_id = $_POST['household_head_id'];

        // Fetch the household head's address
        $result = $conn->query("SELECT address FROM residents WHERE id = $household_head_id AND is_household_head = 1");
        $head = $result->fetch_assoc();
        $address = $head['address'];

        // Insert into database
        $sql = "INSERT INTO residents (fname, mname, lname, ext, gender, bday, age, religion, civil_status, employment_status, voter_status, educ_attainment, contact_num, is_household_head, household_head_id, address, student_level, type_of_work)
                VALUES ('$fname', '$mname', '$lname', '$ext', '$gender', '$bday', '$age', '$religion', '$civil_status', '$employment_status', '$voter_status', '$educ_attainment', '$contact_num', 0, '$household_head_id', '$address', '$student_level', '$type_of_work')";
    }

    // Execute query
    if ($conn->query($sql)) {
        // Redirect to the same page after form submission to avoid resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Ensure no further code is executed after the redirect
    } else {
        echo "Error: " . $conn->error;
    }
}
?>



<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Resident Registration Form</title>
    <script>
        // JavaScript functions to toggle fields (included in your original script)
        function toggleHouseholdFields() {
    const isHouseholdHead = document.getElementById('isHouseholdHead').checked;
    document.getElementById('householdHeadFields').style.display = isHouseholdHead ? 'block' : 'none';
    document.getElementById('householdMemberFields').style.display = isHouseholdHead ? 'none' : 'block';
}

        function toggleWorkField() {
            const employmentStatus = document.getElementById('employmentStatus').value;
            document.getElementById('typeOfWorkField').style.display = employmentStatus === 'employed' ? 'block' : 'none';
            document.getElementById('studentLevelField').style.display = employmentStatus === 'student' ? 'block' : 'none';
        }

        function toggleWaterSource() {
            const ownWater = document.getElementById('ownWater').checked;
            document.getElementById('waterSourceField').style.display = ownWater ? 'block' : 'none';
        }

        function toggleBusinessType() {
            const hasBusiness = document.getElementById('hasBusiness').checked;
            document.getElementById('businessTypeField').style.display = hasBusiness ? 'block' : 'none';
        }
        document.getElementById('householdHeadInput').addEventListener('input', function() {
        const value = this.value;
        const options = document.querySelectorAll('#householdHeads option');
        for (let option of options) {
            if (option.value === value) {
                document.getElementById('householdHeadId').value = option.getAttribute('data-id');
                break;
            } else {
                document.getElementById('householdHeadId').value = ''; // Reset if no match
            }
        }
    });
    </script>
</head>
<body>

<div class="container mt-5">
    <h2>Resident Registration Form</h2>
    <form method="POST">

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>First Name:</label>
                <input type="text" class="form-control" name="fname" required>
            </div>
            <div class="form-group col-md-6">
                <label>Middle Name:</label>
                <input type="text" class="form-control" name="mname" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Last Name:</label>
                <input type="text" class="form-control" name="lname" required>
            </div>
            <div class="form-group col-md-6">
                <label>Extension:</label>
                <input type="text" class="form-control" name="ext">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Gender:</label>
                <select class="form-control" name="gender" required>
                    <option value="">Select  Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Birthday:</label>
                <input type="date" class="form-control" name="bday" required>
            </div>
            <div class="form-group col-md-6">
                <label>Religion:</label>
                <input type="text" class="form-control" name="religion">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Civil Status:</label>
                <select class="form-control" name="civil_status" required>
                    <option value="">Select Civil Status</option>
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widowed">Widowed</option>
                    <option value="divorced">Divorced</option>
                    <option value="single parent">Single Parent</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Employment Status:</label>
                <select class="form-control" name="employment_status" id="employmentStatus" onchange="toggleWorkField()" required>
                    <option value="">Select Employment Status</option>
                    <option value="employed">Employed</option>
                    <option value="unemployed">Unemployed</option>
                    <option value="retired">Retired</option>
                    <option value="student">Student</option>
                </select>
            </div>
        </div>

        <div id="typeOfWorkField" style="display:none;" class="form-group">
            <label>Type of Work:</label>
            <input type="text" class="form-control" name="type_of_work">
        </div>

        <div id="studentLevelField" style="display:none;" class="form-group">
            <label>Student Level:</label>
            <select class="form-control" name="student_level">
                <option value="">Select Student Level</option>
                <option value="preschool">Preschool</option>
                <option value="elementary">Elementary</option>
                <option value="highschool">High School</option>
                <option value="college">College</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Voter Status:</label>
                <select class="form-control" name="voter_status" required>
                    <option value="">Select Voter Status</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Educational Attainment:</label>
                <select class="form-control" name="educ_attainment" required>
                    <option value="">Select Educational Attainment</option>
                    <option value="elementary">Elementary</option>
                    <option value="highschool">High School</option>
                    <option value="college">College</option>
                </select>
            </div>
        </div>

            <div class="form-group col-md-4">
                <label>Contact Number:</label>
                <input type="text" class="form-control" name="contact_num">
            </div>
        </div>

        <div class="form-group">
    <label>Is Household Head?</label>
    <input type="checkbox" name="is_household_head" id="isHouseholdHead" onchange="toggleHouseholdFields()">
</div>

        <!-- Household Head Fields -->
        <div id="householdHeadFields" style="display:none;">
            <h4>Household Head Information</h4>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Family Count:</label>
                    <input type="number" class="form-control" name="fam_count">
                </div>
                <div class="form-group col-md-6">
                    <label>Family Income:</label>
                    <input type="number" step="0.01" class="form-control" name="fam_income">
                </div>
            </div>

            <div class="form-group">
                <label>Address:</label>
                <input type="text" class="form-control" name="address">
            </div>

            <h5>Owned Vehicles:</h5>
            <div class="form-group">
                <input type="checkbox" name="owned_vehicles[]" value="motorcycle"> Motorcycle<br>
                <input type="checkbox" name="owned_vehicles[]" value="tricycle"> Tricycle<br>
                <input type="checkbox" name="owned_vehicles[]" value="car"> Car<br>
                <input type="checkbox" name="owned_vehicles[]" value="bongo"> Bongo<br>
                <input type="checkbox" name="owned_vehicles[]" value="truck"> Truck<br>
                <input type="checkbox" name="owned_vehicles[]" value="multicab"> Multicab<br>
            </div>

            <h5>Owned Appliances:</h5>
            <div class="form-group">
                <input type="checkbox" name="owned_appliances[]" value="refrigerator"> Refrigerator<br>
                <input type="checkbox" name="owned_appliances[]" value="aircon"> Aircon<br>
                <input type="checkbox" name="owned_appliances[]" value="washing_machine"> Washing Machine<br>
                <input type="checkbox" name="owned_appliances[]" value="tv"> TV<br>
                <input type="checkbox" name="owned_appliances[]" value="sound_system"> Sound System<br>
                <input type="checkbox" name="owned_appliances[]" value="computer"> Computer<br>
                <input type="checkbox" name="owned_appliances[]" value="stove"> Stove<br>
            </div>

            <h5>Owned Gadgets:</h5>
            <div class="form-group">
                <input type="checkbox" name="owned_gadgets[]" value="smartphone"> Smartphone<br>
                <input type="checkbox" name="owned_gadgets[]" value="laptop"> Laptop<br>
                <input type="checkbox" name="owned_gadgets[]" value="tablets"> Tablets<br>
            </div>

            <h5>Benefits:</h5>
            <div class="form-group">
                <input type="checkbox" name="benefits[]" value="SSS"> SSS<br>
                <input type="checkbox" name="benefits[]" value="PAG-IBIG"> PAG-IBIG<br>
                <input type="checkbox" name="benefits[]" value="PHILHEALTH"> PHILHEALTH<br>
                <input type="checkbox" name="benefits[]" value="GSIS"> GSIS<br>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>House Ownership:</label><br>
                    <input type="radio" name="house_ownership" value="owned"> Owned<br>
                    <input type="radio" name="house_ownership" value="rental"> Rental<br>
                </div>
                <div class="form-group col-md-6">
                    <label>House Area:</label>
                    <input type="number" class="form-control" name="house_area">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Date Built:</label>
                    <input type="date" class="form-control" name="date_built">
                </div>
                <div class="form-group col-md-6">
                    <label>Own Water:</label>
                    <input type="checkbox" name="own_water" id="ownWater" onchange="toggleWaterSource()"><br>
                </div>
            </div>
            <div id="waterSourceField" style="display:none;" class="form-group">
                <label>Water Source:</label>
                <input type="text" class="form-control" name="water_source">
            </div>

            <div class="form-group">
                <label>Own Electricity:</label>
                <input type="checkbox" name="own_electricity"><br>
            </div>

            <div class="form-group">
                <label>Has Business?</label>
                <input type="checkbox" name="has_business" id="hasBusiness" onchange="toggleBusinessType()"><br>
            </div>
            <div id="businessTypeField" style="display:none;" class="form-group">
                <label>Business Type:</label>
                <input type="text" class="form-control" name="business_type">
            </div>
        </div>

        <!-- Household Member Fields -->
        <div id="householdMemberFields" style="display:none;">
    <h4>Household Member Information</h4>
    <div class="form-group">
        <label>Choose Household Head:</label>
        <input type="text" class="form-control" name="household_head" id="householdHeadInput" list="householdHeads" placeholder="Type to search...">
        <datalist id="householdHeads">
            <?php
            // Fetch household heads for datalist
            $result = $conn->query("SELECT id, fname, lname FROM residents WHERE is_household_head = 1");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['fname']} {$row['lname']}' data-id='{$row['id']}'>{$row['fname']} {$row['lname']}</option>";
            }
            ?>
        </datalist>
        <input type="hidden" name="household_head_id" id="householdHeadId">
    </div>
</div>


        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
