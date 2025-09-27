<?php
// board.php (검색 + 역할 컬럼 추가 버전, view.php로 검색 상태 유지하며 이동)
include '../auth/login_required.php';
require '../config.php'; // $pdo (PDO) 필요
include '../header.php';
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

// 현재 검색 파라미터를 쿼리스트링으로 만들기 (view.php로 보낼 용도)
$preserveQs = http_build_query([
    'q'     => $q,
    'field' => $field,
    'role'  => $role,
]);
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
    a { text-decoration: none; color: #0066cc; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <h1>게시판</h1>

  <!-- 검색 폼 -->
  <form class="search" method="get" action="">
    <label>
      Filter
      <select name="field">
        <option value="title"   <?= $field==='title'?'selected':''; ?>>Title</option>
        <option value="content" <?= $field==='content'?'selected':''; ?>>Contents</option>
        <option value="author"  <?= $field==='author'?'selected':''; ?>>Author</option>
        <option value="all"     <?= $field==='all'?'selected':''; ?>>All(Title+Contents+Author)</option>
      </select>
    </label>
    <label>
      Keyword
      <input type="text" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search">
    </label>
    <label>
      Role
      <select name="role">
        <option value=""        <?= $role===''?'selected':''; ?>>All</option>
        <option value="user"    <?= $role==='user'?'selected':''; ?>>user</option>
        <option value="admin"   <?= $role==='admin'?'selected':''; ?>>admin</option>
      </select>
    </label>
    <button type="submit">Search</button>
    <?php if ($q!=='' || $role!==''): ?>
      <a href="board.php" style="align-self:center">Reset</a>
    <?php endif; ?>
  </form>

  <!-- 목록 -->
  <?php if (!$rows && $q !== ''): ?>
    <p class="no-results">"<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" No Result Found.</p>
  <?php elseif (!$rows): ?>
    <p class="muted">No Content Found.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th style="width:80px">Number</th>
          <th>Title</th>
          <th style="width:160px">Author</th>
          <th style="width:180px">Created_At</th>
          <th style="width:120px">Role</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <?php
            // 안전하게 출력 (제목/작성자 등)
            $id = (int)$r['id'];
            $title = htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8');
            $author = htmlspecialchars($r['author_name'], ENT_QUOTES, 'UTF-8');
            $created = htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8');
            $roleName = htmlspecialchars($r['role_name'], ENT_QUOTES, 'UTF-8');

            // view.php로 보낼 링크 (현재 검색 상태 유지)
            $link = 'view.php?id=' . $id;
            if ($preserveQs !== '') {
                $link .= '&' . $preserveQs;
            }
          ?>
          <tr>
            <td><?= $id ?></td>
            <td><a href="<?= $link ?>"><?= $title ?></a></td>
            <td><?= $author ?></td>
            <td><?= $created ?></td>
            <td><?= $roleName ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
  <a href="/board/write.php">글쓰기</a>
  <a href="/board/profile.php">프로필</a>
  <a href="/auth/logout.php">로그아웃</a>
</body>
</html>
