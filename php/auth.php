<?php
session_start();
require_once 'db.php';

function login($username, $password) {
    global $conn;
    $username = mysqli_real_escape_string($conn, $username);
    $query = "SELECT id, password FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            return true;
        }
    }
    return false;
}

function register($username, $password) {
    global $conn;
    $username = mysqli_real_escape_string($conn, $username);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
    return mysqli_query($conn, $query);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logout() {
    session_start();
    session_destroy();
    header('Location: ../index.php');
    exit();
}
?>