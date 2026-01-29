<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';

Auth::startSession();
if (!Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();


$sql = "SELECT r.*, u.nom AS user_nom, q.titre AS quiz_titre
        FROM results r
        JOIN users u ON r.user_id = u.id
        JOIN quiz q ON r.quiz_id = q.id
        WHERE 1";
$params = [];

if (!empty($_GET['quiz_id'])) {
    $sql .= " AND r.quiz_id = :quiz_id";
    $params[':quiz_id'] = $_GET['quiz_id'];
}
if (!empty($_GET['user_id'])) {
    $sql .= " AND r.user_id = :user_id";
    $params[':user_id'] = $_GET['user_id'];
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=resultats_quiz.csv');


$output = fopen('php://output', 'w');


fputcsv($output, ['Utilisateur', 'Quiz', 'Score', 'Date']);

foreach ($results as $row) {
    fputcsv($output, [
        $row['user_nom'],
        $row['quiz_titre'],
        $row['score'],
        $row['date']
    ]);
}

fclose($output);
exit();
