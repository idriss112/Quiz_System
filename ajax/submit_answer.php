<?php
require_once '../config/Database.php';
require_once '../classes/Auth.php';
require_once '../classes/Question.php';

Auth::startSession();
if (!Auth::isLoggedIn()) {
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$user_id = Auth::getUserId();
$quiz_id = $_POST['quiz_id'] ?? null;
$index = $_POST['index'] ?? 0;
$answer = $_POST['answer'] ?? null;

if (!$quiz_id || $answer === null) {
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$db = (new Database())->connect();
$questionObj = new Question($db);
$questions = $questionObj->getByQuizId($quiz_id);

if (!isset($questions[$index])) {
    echo json_encode(['error' => 'Question introuvable']);
    exit;
}

$isCorrect = trim($answer) === trim($questions[$index]['bonne_reponse']);
echo json_encode(['correct' => $isCorrect]);

// Optional: Store temporary session-based progress or logging here
