<?php
require_once 'config.php';

$file_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

$file_path = 'uploads/' . $file['filename'];

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file['original_filename'] . '"');
header('Content-Length: ' . filesize($file_path));

readfile($file_path);
?>