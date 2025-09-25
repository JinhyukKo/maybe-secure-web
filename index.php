<?php
require_once 'config.php';

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
        <p>환영합니다, <?php echo $_SESSION['username']; ?>님!</p>
        <p>
            <a href="board.php">게시판</a> |
            <a href="write.php">글쓰기</a> |
            <a href="logout.php">로그아웃</a>
        </p>
    <?php else: ?>
        <p>
            <a href="login.php">로그인</a> |
            <a href="register.php">회원가입</a> |
            <a href="board.php">게시판 보기</a>
        </p>
    <?php endif; ?>

    <h2>최근 게시글</h2>
    <ul>
        <?php foreach ($recent_posts as $post): ?>
            <li>
                <a href="view.php?id=<?php echo $post['id']; ?>">
                    <?php echo $post['title']; ?>
                </a>
                - <?php echo $post['username']; ?>
                (<?php echo $post['created_at']; ?>)
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>