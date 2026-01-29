<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

Auth::startSession();
if (!Auth::isLoggedIn() || !Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$userObj = new User($db);
$user = $userObj->getUserById(Auth::getUserId());

$verified = false;
$success = '';

// ✅ Check if verifying password first
if (isset($_POST['verify_password'])) {
    $password = $_POST['verify_password'];
    if ($userObj->verifyPassword($user['id'], $password)) {
        $verified = true;
    } else {
        $success = "Mot de passe incorrect.";
    }
}

// ✅ Check if updating profile
if (isset($_POST['update_profile'])) {
    $verified = true;
    $newName = $_POST['nom'] ?? $user['nom'];
    $newEmail = $_POST['email'] ?? $user['email'];
    $newPassword = $_POST['mot_de_passe'] ?? null;

    if ($newName !== $user['nom']) {
        $stmt = $db->prepare("UPDATE users SET nom = ? WHERE id = ?");
        $stmt->execute([$newName, $user['id']]);
        $user['nom'] = $newName;
        $success = "Nom mis à jour avec succès.";
    }

    if ($newEmail !== $user['email']) {
        $stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$newEmail, $user['id']]);
        $user['email'] = $newEmail;
        $success = "Email mis à jour avec succès.";
    }

    if (!empty($newPassword)) {
        $userObj->updatePassword($user['id'], $newPassword);
        $success = "Mot de passe mis à jour avec succès.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mon Profil (Admin) - QuizApp</title>
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
      max-width: 850px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      color: #2c3e50;
      margin-bottom: 2rem;
    }
    form {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1rem;
    }
    label {
      font-weight: 600;
    }
    input {
      padding: 0.75rem;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
      font-size: 1rem;
      width: 100%;
    }
    button {
      margin-top: 1rem;
      background: #2d89ef;
      color: white;
      border: none;
      padding: 0.75rem;
      border-radius: 0.5rem;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #226bba;
    }
    .success {
      color: green;
      margin-top: 1rem;
      font-weight: 500;
    }
    .error {
      color: red;
      margin-top: 1rem;
      font-weight: 500;
    }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
  <h2>Mon Profil (Administrateur)</h2>

  <?php if (!$verified): ?>
    <form method="POST">
      <label>Veuillez entrer votre mot de passe pour modifier vos informations :</label>
      <input type="password" name="verify_password" placeholder="Mot de passe actuel" required>
      <button type="submit">Valider</button>
    </form>
  <?php else: ?>
    <form method="POST">
      <input type="hidden" name="update_profile" value="1">
      <label>Nom :</label>
      <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

      <label>Email :</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

      <label>Nouveau mot de passe :</label>
      <input type="password" name="mot_de_passe" placeholder="Laisser vide pour ne pas changer">

      <button type="submit">Mettre à jour</button>
    </form>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="<?= $verified ? 'success' : 'error' ?>"><?= $success ?></div>
  <?php endif; ?>
</div>
</body>
</html>
