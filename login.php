<?php
session_start();
include "connect.php"; 

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Get form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    //FETCH DATABASE
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])){
            // Store user info in session
            $_SESSION['user'] = $user['email'];  // Store email in session
            $_SESSION['fullname'] = $user['fullname'];  // Store full name in session
            $_SESSION['member_since'] = $user['member_since'];  // Store member since date in session

            echo "Login successful, Welcome " . $user['fullname'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Wrong Password!!!');
            window.location.href ='index.php';
            </script>";
        }
    } else {
        echo "<script>alert('No user found in the email!');
            window.location.href ='index.php';
            </script>";
    }
}
?>
