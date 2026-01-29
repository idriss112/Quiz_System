<?php
require_once '../config/Database.php';
require_once '../classes/Question.php';

$quiz_id = $_GET['quiz_id'] ?? null;
$index = $_GET['index'] ?? 0;

if (!$quiz_id) {
    echo json_encode(['error' => 'Quiz ID manquant']);
    exit;
}

$db = (new Database())->connect();
$questionObj = new Question($db);
$questions = $questionObj->getByQuizId($quiz_id);

if (!isset($questions[$index])) {
    echo json_encode(['done' => true]);
    exit;
}

$q = $questions[$index];
$options = array_merge(
    [$q['bonne_reponse']],
    explode(',', $q['mauvaises_reponses'])
);
shuffle($options); // Shuffle answers

echo json_encode([
    'question' => $q['question_text'],
    'options' => $options
]);
