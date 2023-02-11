<?php
 
// Create Connection Outside Public Directory
require_once '../connect_to_database.php';

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
 
// Check POST key exists
if (!isset($_POST['password'])) {
	// Exit on Empty POST request
	exit('Please enter the access password.');
}

// Store Hashed Password
$result = mysqli_query($con, "SELECT * FROM accounts WHERE id = 1");
$hashed_password = $result->fetch_object()->password;

// Password Hashing Algorithm
// password_hash($password, PASSWORD_BCRYPT);  

if (password_verify($_POST['password'], $hashed_password)) {

    // Lock Read & Write Access
    mysqli_query($con, "LOCK TABLES Work_Order_Active WRITE;");

    // This SQL statement selects ALL from the table 'Work Order'
    $sql = "SELECT * FROM Work_Order_Active WHERE id=(SELECT max(id) FROM Work_Order_Active)";

    // Check if there are results
    if ($result = mysqli_query($con, $sql))
    {
        $row = $result->fetch_object();
        // Increment Value 
        $number_int = $row->work_order_number + 10;
        $number_str = strval($number_int);

        // Update Table with new Work Order Number
        $sql_insert = $con->prepare("UPDATE Work_Order_Active SET work_order_number=? WHERE id=1");
        $sql_insert -> bind_param("s",$number_str);
        $sql_insert->execute();
        $sql_insert->close();

        // Create JSON to echo to Client
        $resultArray = array();
        $number_int = $row->work_order_number;
        // Create Array of New Work Order Numbers
        for ($x = 0; $x < 10; $x++) {
            $number_int += 1;

            // Add Leading 0 String to Work Order Number
            if ($number_int < 10000) {
                $number_str = "000" . strval($number_int);
            } elseif (10000 <= $number_int && $number_int < 100000){
                $number_str = "00" . strval($number_int);
            } elseif (100000 <= $number_int && $number_int < 1000000){
                $number_str = "0" . strval($number_int);
            } else{
                $number_str = strval($number_int);
            }
            $temp_array = ["work_order_number" => $number_str];
            array_push($resultArray,$temp_array);
        }

        // Finally, encode the array to JSON and output the results
        echo json_encode($resultArray);
    }

    // Unlock Read & Write 
    mysqli_query($con, "UNLOCK TABLES;");

} else {
    // Incorrect username
    echo 'Incorrect username and/or password!';
}
	
// Close connections
mysqli_close($con);

?>