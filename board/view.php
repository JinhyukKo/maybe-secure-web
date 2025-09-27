<?php
include '../auth/login_required.php';
require_once '../config.php';
include '../header.php';
$post_id = (int)($_GET['id'] ?? 0);

// 작성글 + 작성자 role 함께 조회 (비밀글/권한 확인용)
$sql = "SELECT p.*, u.username, u.role AS author_role
        FROM posts p JOIN users u ON p.user_id = u.id
        WHERE p.id = $post_id";
$result = $pdo->query($sql);
$post = $result->fetch();

if (!$post) {
    http_response_code(404);
    exit('존재하지 않는 게시글입니다.');
}

$myId   = $_SESSION['user_id'] ?? 0;
$myRole = $_SESSION['role'] ?? 'user';

$isSecret = (int)$post['is_secret'] === 1;
$isOwner  = ($myId == (int)$post['user_id']);
$isAdmin  = ($myRole === 'admin');

// ✅ 서버 측 강제 차단: 비밀글은 본인 또는 admin만
if ($isSecret && !($isOwner || $isAdmin)) {
    http_response_code(403);
    echo "<h2>비밀글입니다.</h2><p>작성자 또는 관리자만 열람할 수 있습니다.</p>";
    echo '<p><a href="board.php">목록으로</a></p>';
    exit;
}

/* ======================
   댓글 작성/삭제 처리
   ====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 댓글 작성
    if (isset($_POST['comment_content'])) {
        $cmt = $_POST['comment_content'] ?? '';
        $uid = $_SESSION['user_id'];
        if ($uid && trim($cmt) !== '') {
            $sqlIns = "INSERT INTO comments (post_id, user_id, content, created_at)
                       VALUES ($post_id, $uid, '$cmt', NOW())";
            $pdo->query($sqlIns);
        }
        header("Location: view.php?id=" . $post_id);
        exit;
    }

    // 댓글 삭제 (본인 또는 admin만)
    if (isset($_POST['delete_comment_id'])) {
        $cid = (int)$_POST['delete_comment_id'];
        // 해당 댓글 소유자 확인
        $row = $pdo->query("SELECT user_id FROM comments WHERE id = $cid")->fetch();
        if ($row && ($isAdmin || (int)$row['user_id'] === (int)$_SESSION['user_id'])) {
            $pdo->query("DELETE FROM comments WHERE id = $cid");
        }
        header("Location: view.php?id=" . $post_id);
        exit;
    }
}

// 댓글 목록
$comments = $pdo->query("
    SELECT c.id, c.content, c.created_at, c.user_id, u.username
    FROM comments c JOIN users u ON c.user_id = u.id
    WHERE c.post_id = $post_id
    ORDER BY c.created_at ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></title>
    <style>
      .comment { border-top:1px solid #eee; padding:8px 0; }
      .comment .meta { color:#666; font-size:12px; }
      .comment-actions { margin-left:8px; display:inline-block; }
      textarea { width:100%; max-width:700px; }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h1>

    <p>
        <a href="index.php">메인</a> |
        <a href="board.php">게시판</a>
    </p>

    <p>작성자: <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8') ?>
       | 작성일: <?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?>
       <?php if ($isSecret): ?>
         | <strong>🔒 비밀글</strong>
       <?php endif; ?>
    </p>

    <div>
        <?= nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')) ?>
    </div>

    <?php if($post['filename']): ?>
        <p><a href="uploads/<?= htmlspecialchars($post['filename'], ENT_QUOTES, 'UTF-8')?>" download>
            <?= htmlspecialchars($post['filename'], ENT_QUOTES, 'UTF-8')?>
        </a></p>
    <?php endif; ?>

    <p>
        <a href="board.php">목록으로</a> |
        <?php if ($isOwner || $isAdmin): ?>
          <a href="delete.php?id=<?= (int)$post['id']; ?>" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
        <?php endif; ?>
    </p>

    <!-- ======================
         댓글 영역
         ====================== -->
    <h2>댓글</h2>

    <?php if ($comments): ?>
      <?php foreach ($comments as $c): ?>
        <div class="comment">
          <div class="meta">
            <?= htmlspecialchars($c['username'], ENT_QUOTES, 'UTF-8') ?>
            (<?= htmlspecialchars($c['created_at'], ENT_QUOTES, 'UTF-8') ?>)
          </div>
          <div class="body">
            <?= nl2br(htmlspecialchars($c['content'], ENT_QUOTES, 'UTF-8')) ?>
            <?php if ($isAdmin || (int)$c['user_id'] === (int)$_SESSION['user_id']): ?>
              <span class="comment-actions">
                <form method="post" style="display:inline">
                  <input type="hidden" name="delete_comment_id" value="<?= (int)$c['id'] ?>">
                  <button type="submit" onclick="return confirm('댓글을 삭제할까요?')">삭제</button>
                </form>
              </span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="muted">댓글이 없습니다.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
      <h3>댓글 작성</h3>
      <form method="post">
        <p>
          <textarea name="comment_content" rows="4" placeholder="댓글을 입력하세요"></textarea>
        </p>
        <p><button type="submit">등록</button></p>
      </form>
    <?php else: ?>
      <p class="muted">댓글 작성은 로그인 후 가능합니다.</p>
    <?php endif; ?>
</body>
</html>
