<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Quiz.php';

Auth::startSession();
if (!Auth::isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$quiz = new Quiz($db);
$quizzes = $quiz->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])) {
    header("Location: play_quiz.php?quiz_id=" . $_POST['quiz_id']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Sélection du Quiz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    select, button {
      width: 100%;
      padding: 0.75rem;
      margin-top: 1rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
    }
    button {
      background-color: #2d89ef;
      color: white;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Choisir un Quiz</h2>
    <form method="POST">
      <select name="quiz_id" required>
        <option value="">-- Sélectionnez un quiz --</option>
        <?php foreach ($quizzes as $q): ?>
          <option value="<?= $q['id'] ?>"><?= htmlspecialchars($q['titre']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Commencer</button>
    </form>
  </div>
</body>
</html>
