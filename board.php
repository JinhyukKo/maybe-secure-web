<?php
require_once 'config.php';

$stmt = $pdo->query("
    SELECT p.id, p.title, u.username, p.created_at
    FROM posts p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>게시판</title>
</head>
<body>
    <h1>게시판</h1>

    <p>
        <a href="index.php">메인</a> |
        <a href="write.php">글쓰기</a>
    </p>

    <table border="1">
        <tr>
            <th>번호</th>
            <th>제목</th>
            <th>작성자</th>
            <th>작성일</th>
        </tr>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?php echo $post['id']; ?></td>
            <td><a href="view.php?id=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></td>
            <td><?php echo $post['username']; ?></td>
            <td><?php echo $post['created_at']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>