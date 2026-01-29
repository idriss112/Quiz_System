<?php
require_once 'config/Database.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';

Auth::startSession();
if (!Auth::isLoggedIn() || Auth::isAdmin()) {
    header('Location: login.php');
    exit();
}

$db = (new Database())->connect();
$userObj = new User($db);
$user = $userObj->getUserById(Auth::getUserId());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord - Utilisateur</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      background: #f4f6f9;
    }
    .container {
      padding: 2rem;
      max-width: 900px;
      margin: auto;
    }
    h2 {
      color: #2c3e50;
      margin-bottom: 1rem;
    }
    .btn {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      background-color: #2d89ef;
      color: white;
      border: none;
      border-radius: 0.5rem;
      font-size: 1rem;
      text-decoration: none;
      margin-top: 1rem;
      margin-right: 1rem;
      transition: background 0.3s;
    }
    .btn:hover {
      background-color: #226bba;
    }
  </style>
</head>
<body>

<?php include 'header_user.php'; ?>

<div class="container">
  <h2>Bienvenue sur votre tableau de bord, <?= htmlspecialchars($user['nom']) ?> 👋</h2>
  <p>Commencez un nouveau quiz ou consultez vos résultats précédents :</p>

  <a href="quiz_list.php" class="btn">Liste des Quiz</a>
  <a href="user_results.php" class="btn">Mes Résultats</a>
</div>

</body>
</html>
