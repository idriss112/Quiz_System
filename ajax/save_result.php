<?php
require_once '../config/Database.php';
require_once '../classes/Auth.php';

Auth::startSession();
if (!Auth::isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = Auth::getUserId();
$quiz_id = $_POST['quiz_id'] ?? null;
$score = $_POST['score'] ?? null;

if (!$quiz_id || $score === null) {
    echo json_encode(['error' => 'Missing data']);
    exit();
}

$db = (new Database())->connect();
$stmt = $db->prepare("INSERT INTO results (user_id, quiz_id, score) VALUES (?, ?, ?)");
$success = $stmt->execute([$user_id, $quiz_id, $score]);

echo json_encode(['success' => $success]);
