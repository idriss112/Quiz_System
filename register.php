<?php
require_once 'config/Database.php';
require_once 'classes/User.php';

$db = (new Database())->connect();
$user = new User($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($user->register($nom, $email, $password, 'participant')) {
      $message = "Inscription réussie. Vous pouvez maintenant vous connecter.";
  } else {
      $message = "Erreur lors de l'inscription. Essayez un autre email.";
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - QuizApp</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #f4f6f9;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    .container {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 450px;
      margin: 4rem auto;
    }
    h2 {
      margin-bottom: 1.5rem;
      color: #2c3e50;
      text-align: center;
    }
    input {
      width: 100%;
      padding: 0.75rem;
      margin: 0.5rem 0 1rem;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
      font-size: 1rem;
    }
    button {
      width: 100%;
      padding: 0.75rem;
      background-color: #2d89ef;
      color: white;
      border: none;
      border-radius: 0.5rem;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: #256ad3;
    }
    .message {
      margin-top: 1rem;
      color: green;
      text-align: center;
    }
    .link-box {
      text-align: center;
      margin-top: 1rem;
    }
    .link-box a {
      color: #2d89ef;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>

<?php include 'header_guest.php'; ?>

<div class="container">
  <h2>Créer un compte</h2>
  <form method="POST">
    <input type="text" name="nom" placeholder="Nom complet" required>
    <input type="email" name="email" placeholder="Adresse email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">S'inscrire</button>
  </form>

  <div class="link-box">
    Vous avez déjà un compte ? <a href="login.php">Connexion</a>
  </div>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
</div>

</body>
</html>
