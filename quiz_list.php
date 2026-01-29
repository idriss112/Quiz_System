<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Quiz.php';

Auth::startSession();
if (!Auth::isLoggedIn() || Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$quiz = new Quiz($db);
$quizzes = $quiz->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Quizz Disponibles</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }
    .container {
      padding: 2rem;
      max-width: 1100px;
      margin: auto;
    }
    .header {
      text-align: center;
      margin-bottom: 2rem;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 2rem;
    }
    .card {
      background: white;
      padding: 1.5rem;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .card h3 {
      margin: 0 0 0.5rem 0;
      color: #2c3e50;
    }
    .card p {
      flex-grow: 1;
      color: #555;
    }
    .card a {
      display: inline-block;
      margin-top: 1rem;
      padding: 0.6rem 1rem;
      background-color: #2d89ef;
      color: white;
      text-align: center;
      text-decoration: none;
      border-radius: 0.5rem;
    }
    .card a:hover {
      background-color: #256ad3;
    }
  </style>
</head>
<body>

<?php include 'header_user.php'; ?>

<div class="container">
  <div class="header">
    <h2>Parcourez les Quiz</h2>
    <p>Choisissez un quiz et testez vos connaissances</p>
  </div>

  <div class="grid">
    <?php foreach ($quizzes as $q): ?>
      <div class="card">
        <h3><?= htmlspecialchars($q['titre']) ?></h3>
        <p><?= htmlspecialchars($q['description']) ?></p>
        <a href="play_quiz.php?quiz_id=<?= $q['id'] ?>">Commencer</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>
