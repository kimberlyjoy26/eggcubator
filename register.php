<?php
include "connect.php"; // Added semicolon here

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Get form data
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    
    
    // Check if email is already in database
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result =  $conn->query($checkEmail); // Corrected variable name to $checkEmail

    if($result->num_rows > 0) {
        echo "<script>alert('Email already exist!!!');
        window.location.href = 'index.php';
        </script>";
    } else {

       
       
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users(fullname,email,password) VALUES ('$fullname', '$email', '$hashed_password')";

        if($conn->query($sql) === TRUE) {
            //account created
            echo "<script>alert('Account Created!!!');
            window.location.href ='index.php';
            </script>";





        } else {
            echo "<script>
                    alert('Error: " . $conn->error . "');
                    window.history.back();
                  </script>";
        }
        
    }
}
?>
