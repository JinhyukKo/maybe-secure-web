<?php
include './logout_required.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password]);
    echo "회원가입 완료. <a href='login.php'>로그인</a>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>

    <form method="POST">
        <p>
            username: <br>
            <input type="text" name="username">
        </p>
        <p>
            email: <br>
            <input type="email" name="email">
        </p>
        <p>
            password: <br>
            <input type="password" name="password">
        </p>
        <p>
            <input type="submit" value="회원가입">
        </p>
    </form>

    <p><a href="login.php">Login</a>
</body>
</html>