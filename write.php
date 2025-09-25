<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $user_id]);
    $post_id = $pdo->lastInsertId();

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        if (!file_exists('uploads')) {
            mkdir('uploads');
        }

        $filename = uniqid() . '_' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $filename);

        $stmt = $pdo->prepare("INSERT INTO files (post_id, filename, original_filename, file_size) VALUES (?, ?, ?, ?)");
        $stmt->execute([$post_id, $filename, $_FILES['file']['name'], $_FILES['file']['size']]);
    }

    header("Location: view.php?id=" . $post_id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>글쓰기</title>
</head>
<body>
    <h1>글쓰기</h1>

    <p>
        <a href="index.php">메인</a> |
        <a href="board.php">게시판</a>
    </p>

    <form method="POST" enctype="multipart/form-data">
        <p>
            제목: <br>
            <input type="text" name="title" size="50">
        </p>

        <p>
            내용: <br>
            <textarea name="content" rows="10" cols="60"></textarea>
        </p>

        <p>
            파일: <br>
            <input type="file" name="file">
        </p>

        <p>
            <input type="submit" value="작성">
        </p>
    </form>
</body>
</html>