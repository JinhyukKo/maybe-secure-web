<?php
require_once '/config.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        echo "로그인 실패";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>로그인</title>
</head>
<body>
    <h1>로그인</h1>

    <form method="POST">
        <p>
            사용자명: <br>
            <input type="text" name="username">
        </p>
        <p>
            비밀번호: <br>
            <input type="password" name="password">
        </p>
        <p>
            <input type="submit" value="로그인">
        </p>
    </form>

    <p><a href="register.php">회원가입</a> | <a href="index.php">메인</a></p>
</body>
</html>