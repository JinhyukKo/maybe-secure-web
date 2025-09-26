<?php
// board.php (검색 + 역할 컬럼 추가 버전)
session_start();
require __DIR__ . '/db.php'; // $pdo (PDO) 필요

// --- 입력 파라미터 ---
$q      = isset($_GET['q']) ? trim($_GET['q']) : '';
$field  = isset($_GET['field']) ? $_GET['field'] : 'title'; // title|content|author|all
$role   = isset($_GET['role']) ? trim($_GET['role']) : '';  // user|admin (옵션)

// 화이트리스트
$validFields = ['title','content','author','all'];
if (!in_array($field, $validFields, true)) $field = 'title';

// --- 동적 WHERE ---
$where = [];
$params = [];

if ($q !== '') {
    switch ($field) {
        case 'title':
            $where[] = "p.title LIKE '%$q%'";
            break;
        case 'content':
            $where[] = "p.content LIKE '%$q%'";
            break;
        case 'author':
            $where[] = "u.username LIKE '%$q%'";
            break;
        case 'all':
        default:
            $where[] = "(p.title LIKE '%$q%' OR p.content LIKE '%$q%' OR u.username LIKE '%$q%')";
            break;
    }
}

if ($role !== '') {
    $where[] = "COALESCE(p.role, u.role) = '$role'";
}

$whereSql = $where ? ('WHERE '.implode(' AND ', $where)) : '';

// --- 조회 SQL ---
$sql = "
SELECT
    p.id,
    p.title,
    p.created_at,
    u.username AS author_name,
    COALESCE(p.role, u.role) AS role_name
FROM posts p
JOIN users u ON p.user_id = u.id
{$whereSql}
ORDER BY p.created_at DESC, p.id DESC
";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <title>게시판</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
    form.search { margin: 16px 0; display: flex; gap: 8px; flex-wrap: wrap; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    thead th { background: #f7f7f7; }
    .muted { color:#666; }
    input[type="text"] { padding: 6px 8px; width: 260px; }
    select { padding: 6px 8px; }
    button { padding: 6px 12px; cursor: pointer; }
    .no-results { color: #999; font-style: italic; margin: 20px 0; }
  </style>
</head>
<body>
  <h1>게시판</h1>

  <!-- 검색 폼 -->
  <form class="search" method="get" action="">
    <label>
      검색구분
      <select name="field">
        <option value="title"   <?= $field==='title'?'selected':''; ?>>제목</option>
        <option value="content" <?= $field==='content'?'selected':''; ?>>내용</option>
        <option value="author"  <?= $field==='author'?'selected':''; ?>>작성자</option>
        <option value="all"     <?= $field==='all'?'selected':''; ?>>전체(제목+내용+작성자)</option>
      </select>
    </label>
    <label>
      키워드
      <input type="text" name="q" value="<?= $q ?>" placeholder="검색어 입력">
    </label>
    <label>
      역할
      <select name="role">
        <option value=""        <?= $role===''?'selected':''; ?>>전체</option>
        <option value="user"    <?= $role==='user'?'selected':''; ?>>user</option>
        <option value="admin"   <?= $role==='admin'?'selected':''; ?>>admin</option>
      </select>
    </label>
    <button type="submit">검색</button>
    <?php if ($q!=='' || $role!==''): ?>
      <a href="board.php" style="align-self:center">초기화</a>
    <?php endif; ?>
  </form>

  <!-- 목록 -->
  <?php if (!$rows && $q !== ''): ?>
    <p class="no-results">"<?= $q ?>"에 대한 검색 결과가 없습니다.</p>
  <?php elseif (!$rows): ?>
    <p class="muted">게시글이 없습니다.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th style="width:80px">번호</th>
          <th>제목</th>
          <th style="width:160px">작성자</th>
          <th style="width:180px">작성일</th>
          <th style="width:120px">역할</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['title'] ?></td>
            <td><?= $r['author_name'] ?></td>
            <td><?= $r['created_at'] ?></td>
            <td><?= $r['role_name'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>