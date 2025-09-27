<?php
include './auth/login_required.php';
require_once './config.php';
include "./header.php";


$stmt = $pdo->query("
    SELECT p.id, p.title, u.username, p.created_at
    FROM posts p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
    LIMIT 5
");
$recent_posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>간단한 게시판</title>
</head>
<body>
    <h1>간단한 게시판</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php header("Location: /board/board.php"); ?>
    <?php else: ?>
        <p>
            <a href="/auth/login.php">로그인</a> |
            <a href="/auth/register.php">회원가입</a> |
            <a href="/board/board.php">게시판 보기</a>
        </p>
    <?php endif; ?>

    
</body>
</html>