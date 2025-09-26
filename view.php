<?php
require_once 'config.php';
include __DIR__ . "/header.php";


$post_id = $_GET['id'];

$sql = "SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = $post_id";
$result = $pdo->query($sql);
$post = $result->fetch();

$sql = "SELECT * FROM files WHERE post_id = $post_id";
$result = $pdo->query($sql);
$files = $result->fetchAll();
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
                <a href="uploads/<?php echo $file['real_filename']; ?>" download>
                    <?php echo $file['real_filename']; ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p>
        <a href="board.php">목록으로</a> |
        <a href="delete.php?id=<?php echo $post['id']; ?>" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
    </p>
</body>
</html>