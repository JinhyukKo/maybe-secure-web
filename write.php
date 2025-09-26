<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $user_id = $_SESSION['user_id'];
    $filename = '';

    if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK){
        $filename = $_FILES['upload']['name'];
        $tmp_name = $_FILES['upload']['tmp_name'];
        move_uploaded_file($tmp_name, "uploads/" . $filename);
    }

    $sql = "INSERT INTO posts (user_id, title, content, filename) values($user_id, '$title', '$content', '$filename')";

    $pdo->query($sql);

    header("Location: index.php");
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
            <input type="file" name="upload">
        </p>

        <p>
            <input type="submit" value="작성">
        </p>
    </form>
</body>
</html>