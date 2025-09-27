<?php
include '../auth/checkauth.php';
require_once '../config.php';
include '../header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title   = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $user_id = $_SESSION['user_id'];
    $filename = '';

    // ✅ 비밀글 체크박스
    $is_secret = isset($_POST['is_secret']) ? 1 : 0;

    if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK){
        $filename = $_FILES['upload']['name'];
        $tmp_name = $_FILES['upload']['tmp_name'];
        @mkdir(__DIR__ . "/uploads", 0777, true);
        move_uploaded_file($tmp_name, __DIR__ . "/uploads/" . $filename);
    }

    // ✅ is_secret 저장 (기존 스타일 유지: 단순 쿼리)
    $sql = "INSERT INTO posts (user_id, title, content, filename, is_secret)
            VALUES ($user_id, '$title', '$content', '$filename', $is_secret)";
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

        <!-- ✅ 비밀글 토글 -->
        <p>
            <label>
                <input type="checkbox" name="is_secret" value="1">
                비밀글로 설정 (작성자와 관리자만 열람)
            </label>
        </p>

        <p>
            <input type="submit" value="작성">
        </p>
    </form>
</body>
</html>
