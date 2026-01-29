<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';
require_once 'classes/Quiz.php';
require_once 'classes/Question.php';

Auth::startSession();
if (!Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$userObj = new User($db);
$quizObj = new Quiz($db);
$questionObj = new Question($db);
$admin = $userObj->getUserById(Auth::getUserId());

$totalUsers = $userObj->countAll();
$totalQuizzes = $quizObj->countAll();
$totalQuestions = $questionObj->countAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - QuizApp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; margin: 0; }
    header {
      background-color: #2d89ef;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo { font-size: 1.25rem; font-weight: 600; }
    nav a {
      color: white;
      margin-left: 1rem;
      text-decoration: none;
      font-weight: 500;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      padding: 2rem;
    }
    .welcome {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .stats {
      display: flex;
      gap: 2rem;
      justify-content: space-between;
    }
    .card {
      background: white;
      flex: 1;
      padding: 1.5rem;
      border-radius: 1rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      text-align: center;
    }
    .card h3 {
      margin-bottom: 0.5rem;
      color: #2d89ef;
    }
    .card p {
      font-size: 2rem;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">QuizApp</div>
    <nav>
      <a href="admin_manage_quiz.php">Gérer les quiz</a>
      <a href="admin_manage_questions.php">Gérer les questions</a>
      <a href="admin_manage_users.php">Gérer les utilisateurs</a>
      <a href="admin_results.php">Gérer les resultats</a>
      <a href="profile.php">Profil</a>
      <a href="logout.php">Déconnexion</a>
    </nav>
  </header>

  <div class="container">
    <div class="welcome">
      <h2>Bienvenue de retour, Admin <?= htmlspecialchars($admin['nom']) ?> !</h2>
    </div>

    <div class="stats">
      <div class="card">
        <h3>Utilisateurs</h3>
        <p><?= $totalUsers ?></p>
      </div>
      <div class="card">
        <h3>Quiz</h3>
        <p><?= $totalQuizzes ?></p>
      </div>
      <div class="card">
        <h3>Questions</h3>
        <p><?= $totalQuestions ?></p>
      </div>
    </div>
  </div>
</body>
</html>
