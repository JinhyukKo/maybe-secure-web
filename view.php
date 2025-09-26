<?php
require_once 'config.php';
include __DIR__ . "/header.php";


$post_id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT p.*, u.username
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM files WHERE post_id = ?");
$stmt->execute([$post_id]);
$files = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $post['title']; ?></title>
</head>
<body>
    <h1><?php echo $post['title']; ?></h1>

    <p>
        <a href="index.php">메인</a> |
        <a href="board.php">게시판</a>
    </p>

    <p>작성자: <?php echo $post['username']; ?> | 작성일: <?php echo $post['created_at']; ?></p>

    <div>
        <?php echo nl2br($post['content']); ?>
    </div>

    <?php if ($files): ?>
        <h3>첨부파일</h3>
        <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <a href="download.php?id=<?php echo $file['id']; ?>">
                    <?php echo $file['original_filename']; ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="board.php">목록으로</a></p>
</body>
</html>