<?php
$DATABASE_HOST = 'sql302.infinityfree.com';
$DATABASE_USER = 'if0_35194881';
$DATABASE_PASS = 'l1F2IxfjP0';
$DATABASE_NAME = 'if0_35194881_moon';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}


//Login from loader
if (isset($_GET['login'])) { 
    if (isset($_GET['username'])) { $username = $_GET['username']; }
    if (isset($_GET['password'])) { $password = $_GET['password']; }
    
    
    
    // Query the database for the user
    $query = 'SELECT * FROM `users` WHERE `username` = ?';
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Verify the password
        if ($password === $row['password']) { // Note: In production, you should use password hashing and verification
            if($row['admin'] === 1) {
                echo "admin";
            }
            else {
                echo "normal";
            }
           
        } else {
            // Incorrect password
            echo "invalid_password";
        }
    } else {
        // User not found
        echo "not_found";
    }

    mysqli_stmt_close($stmt);
 }


 //Login from loader
 if (isset($_GET['register'])) {
    if (isset($_GET['username']) && isset($_GET['password']) && isset($_GET['code'])) {
        $username = $_GET['username'];
        $password = $_GET['password'];
        $code = $_GET['code'];

        // Check if the username already exists in the database
        $checkQuery = 'SELECT COUNT(*) FROM `users` WHERE `username` = ?';
        $checkStmt = mysqli_prepare($con, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, 's', $username);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        // Check if the code exists in the "codes" table
        $codeQuery = 'SELECT COUNT(*) FROM `codes` WHERE `code` = ?';
        $codeStmt = mysqli_prepare($con, $codeQuery);
        mysqli_stmt_bind_param($codeStmt, 's', $code);
        mysqli_stmt_execute($codeStmt);
        mysqli_stmt_bind_result($codeStmt, $codeCount);
        mysqli_stmt_fetch($codeStmt);
        mysqli_stmt_close($codeStmt);

        if ($count > 0) {
            // Username already exists
            echo "Username already exists.";
        } elseif ($codeCount == 0) {
            // Code does not exist in "codes" table
            echo "Key not found.";
        } else {
            // Username does not exist, and the code exists; insert user data into the database
            $insertQuery = 'INSERT INTO `users` (`username`, `password`, `activation_code`) VALUES (?, ?, ?)';
            $insertStmt = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, 'sss', $username, $password, $code);

            if (mysqli_stmt_execute($insertStmt)) {
                // Registration successful; delete the key from the "codes" table
                $deleteQuery = 'DELETE FROM `codes` WHERE `code` = ?';
                $deleteStmt = mysqli_prepare($con, $deleteQuery);
                mysqli_stmt_bind_param($deleteStmt, 's', $code);
                mysqli_stmt_execute($deleteStmt);

                echo "success";
            } else {
                echo "invalid";
            }

            mysqli_stmt_close($insertStmt);
        }
    } else {
        echo "Invalid parameters. Please provide both username, password, and code.";
    }
}



//Admin from loader
if (isset($_GET['gencode'])) { 
    if (isset($_GET['username'])) { $username = $_GET['username']; }
    if (isset($_GET['password'])) { $password = $_GET['password']; }

    if (isset($_GET['count'])) {
        $count = intval($_GET['count']); // Ensure $count is an integer
    
    
    
    // Query the database for the user
    $query = 'SELECT * FROM `users` WHERE `username` = ?';
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Verify the password
        if ($password === $row['password']) { // Note: In production, you should use password hashing and verification
            if($row['admin'] === 1) {
                echo "generated";
                for ($i = 0; $i < $count; $i++) {
                    $randomCode = generateRandomCode(); // Function to generate a random code
                    insertCodeIntoDatabase($con, $randomCode, $username);
                }
            }
            else {
                echo "error";
            }
           
        } else {
            // Incorrect password
            echo "invalid_password";
        }
    } else {
        // User not found
        echo "not_found";
    }

    mysqli_stmt_close($stmt);
 }
}



 function generateRandomCode() {
    // Generate a random code here and return it
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';
    $codeLength = 10; // You can adjust the code length as needed

    for ($i = 0; $i < $codeLength; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $code;
}

function insertCodeIntoDatabase($con, $code) {
    // Insert the generated code into the database here
    $insertQuery = 'INSERT INTO `codes` (`code`) VALUES (?)';
    $insertStmt = mysqli_prepare($con, $insertQuery);
    mysqli_stmt_bind_param($insertStmt, 'ss', $code);
    mysqli_stmt_execute($insertStmt);
    mysqli_stmt_close($insertStmt);
}





 if (isset($_GET['hashwid'])) { 
    if (isset($_GET['username'])) { $hwidusername = $_GET['username']; }
    
    
    
            // Check if the username already exists in the database
            $checkQuery = 'SELECT `hwid` FROM `users` WHERE `username` = ?';
            $checkStmt = mysqli_prepare($con, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, 's', $username);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_bind_result($checkStmt, $count);
            mysqli_stmt_fetch($checkStmt);
            mysqli_stmt_close($checkStmt);
    
    if ($count) {
        echo "TRUE";
    } else {
        echo "FALSE";
    }
 }










 if (isset($_GET['hwid'])) {
    if (isset($_GET['username'])) { $hwidsetusername = $_GET['username']; }
    if (isset($_GET['password'])) { $hwidsetpassword = $_GET['password']; }
    if (isset($_GET['hardware'])) { $lasthwidvalue = $_GET['hardware']; }

    // Check if the username and password are correct (you should use password hashing for security)
    $query = 'SELECT * FROM `users` WHERE `username` = ? AND `password` = ?';
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $hwidsetusername, $hwidsetpassword);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Username and password are correct
        $storedHwid = $row['hwid'];

        if (empty($storedHwid)) {
            // If the stored HWID is empty, update it with the provided HWID
            updateHardwareInfo($con, $lasthwidvalue, $hwidsetusername);
            echo "new_hwid";
        } elseif ($lasthwidvalue === $storedHwid) {
            // If the provided HWID matches the stored HWID, update hardware information
            updateHardwareInfo($con, $lasthwidvalue, $hwidsetusername);
            echo "updated_hwid";
        } else {
            // HWID does not match
            echo "invalid_hwid";
        }
    } else {
        // Incorrect username or password
        echo "invalid_credentials";
    }

    mysqli_stmt_close($stmt);
}

function updateHardwareInfo($con, $newHwid, $username) {
    // Update the hardware information in the database here
    $updateQuery = 'UPDATE `users` SET `hwid` = ? WHERE `username` = ?';
    $updateStmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, 'ss', $newHwid, $username);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);
}




 if (isset($_GET['email'])) { 
    if (isset($_GET['username'])) { $username = $_GET['username']; }
    
    $email = $user->email($username);
    
    echo $email;
 }

 if (isset($_GET['sub'])) { 
    if (isset($_GET['username'])) { $username = $_GET['username']; }
    
    $sub = $user->has_sub($username);
    
    echo $sub;
 }

 if (isset($_GET['created'])) { 
    if (isset($_GET['username'])) { $username = $_GET['username']; }
    
    $created = $user->created($username);
    
    echo $created;
 }


 if (isset($_GET['version'])) { 
   echo "0.9";
}




?>


