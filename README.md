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

Fill in the MySQL connect parameters with your database credentials. It's recommended to locate the database connection file outside the public directory.
```php
<?php

$con = mysqli_connect("hostname", "username", "password", "database");

?>
```
In the public php file require once the connect to database file. Once the PHP file is required, the function will run and the connection will be made. 

The require once will also allow the '$con' variable to be accessible in the public file.

```php
<?php

require_once '../connect_to_database.php';
```

Confirm the database connection has been established.

```php
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
```

Check if the POST password has been provided. If not, exit the application.

```php
if (!isset($_POST['password'])) {
	// Exit on Empty POST request
	exit('Please enter the access password.');
}
```

Make a request from the accounts table to SELECT the password from the database. All passwords should be hashed when stored.

```php
$result = mysqli_query($con, "SELECT * FROM accounts WHERE id = 1");
$hashed_password = $result->fetch_object()->password;
```

The hashed password is verified against the POST request. If the password is correct, the user is authenticated and the SQL table can be updated.

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
// End of File
?>
```

That's it. You should now be ready to use MySQL-Concurrency Codebase!


## License

This software is distributed under the MIT license. Please read [LICENSE](https://github.com/andrew-drogalis/MySQL-Concurrency/blob/main/LICENSE) for information on the software availability and distribution.

## Contact | Contribution

Please open an issue of if you have any questions, suggestions, or feedback.

Please submit bug reports, suggestions, and pull requests to the [GitHub issue tracker](https://github.com/andrew-drogalis/MySQL-Concurrency/issues).

Contact Email: [**Andrew Drogalis**](mailto:andrew.drogalis2@gmail.com) 



