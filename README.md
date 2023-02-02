# MySQL-Concurrency

## Table of Contents
* [Feautures](#Feautures)
* [Example Overview](#Example-Overview)
* [License](#License)
* [Contact | Contribution](#Contact-|-Contribution)

## Features
- Updates My SQL Database concurrently using WRITE LOCK or READ LOCK
- Password Hashing and Password Verification
- Check Database Connection & Client POST request
- Stores Database Credentials outside Public Directory

## Example Overview

Below is an example based on the code provided in the [src](https://github.com/andrew-drogalis/MySQL-Concurrency/blob/main/src/concurrency_example.php) folder, which covers updating a MySQL database concurrently, verifing user submitted passwords, and performing SQL data queries.

Locate the database credentials outside the public directory.
```php
<?php

$con = mysqli_connect("hostname", "username", "password", "database");

?>
```
Once the PHP file is required, the 'database_connect.php' will run and open the connection. 

The '$con' variable will be available in the src file.

```php
<?php

require_once '../database_connect.php';
```

Confirm the database connection has been established.

```php
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
```

If the POST password has not been provided, exit the application.

```php
if (!isset($_POST['password'])) {
	// Exit on Empty POST request
	exit('Please enter the access password.');
}
```

Make a request from the accounts table to SELECT the previously hashed password.

```php
$result = mysqli_query($con, "SELECT * FROM accounts WHERE id = 1");
$hashed_password = $result->fetch_object()->password;
```

The hashed password is verified against the POST request.

If the password is correct, the user is authenticated and the SQL table can be updated.

If concurrency is required, the LOCK TABLES query should be implimented. The WRITE parameter prevents any other session from reading or writing from the locked table. The READ parameter prevents another session from writting to the locked table, but allows read access.

```php
if (password_verify($_POST['password'], $hashed_password)) {

    // Lock Read & Write Access
    mysqli_query($con, "LOCK TABLES Work_Order_Active WRITE;");

    // Lock Write Access Only
    mysqli_query($con, "LOCK TABLES Work_Order_Active READ;");
```
Run the SELECT query and store the results. If the result is not empty, fetch the object and perform the required calculations. 

Once the calculations are complete, UPDATE the table with the new data and echo the JSON to the user.

```php
// This SQL statement selects ALL from the table 'Work Order'
$sql = "SELECT * FROM Work_Order_Active WHERE id=(SELECT max(id) FROM Work_Order_Active)";

if ($result = mysqli_query($con, $sql))
{
    $row = $result->fetch_object();
    
    ...

    // Update Table with new Work Order Number
    $sql_insert = $con->prepare("UPDATE Work_Order_Active SET work_order_number=? WHERE id=1");
    $sql_insert -> bind_param("s",$number_str);
    $sql_insert->execute();
    $sql_insert->close();

    ...

    // Finally, encode the array to JSON and output the results
    echo json_encode($resultArray);
}
```

After the table has been updated, the UNLOCK TABLES query can be sent.

```php
mysqli_query($con, "UNLOCK TABLES;");
```

Once all the queries have been made, make sure to close the connection to the MySQL database.

```php
mysqli_close($con);

?>
```

That's it. You should now be ready to use MySQL-Concurrency Codebase!


## License

This software is distributed under the MIT license. Please read [LICENSE](https://github.com/andrew-drogalis/MySQL-Concurrency/blob/main/LICENSE) for information on the software availability and distribution.

## Contact | Contribution

Please open an issue of if you have any questions, suggestions, or feedback.

Please submit bug reports, suggestions, and pull requests to the [GitHub issue tracker](https://github.com/andrew-drogalis/MySQL-Concurrency/issues).

Contact Email: [**Andrew Drogalis**](mailto:andrew.drogalis@gmail.com) 



