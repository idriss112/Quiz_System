<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

Auth::startSession();
if (!Auth::isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$userObj = new User($db);
$user = $userObj->getUserById(Auth::getUserId());

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = $_POST['nom'] ?? $user['nom'];
    $newEmail = $_POST['email'] ?? $user['email'];
    $newPassword = $_POST['mot_de_passe'] ?? null;
    $oldPassword = $_POST['ancien_mot_de_passe'] ?? null;

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
        if (empty($oldPassword)) {
            $success = "Veuillez entrer votre mot de passe actuel.";
        } elseif (!$userObj->verifyPassword($user['id'], $oldPassword)) {
            $success = "Mot de passe actuel incorrect.";
        } else {
            $userObj->updatePassword($user['id'], $newPassword);
            $success = "Mot de passe mis à jour avec succès.";
        }
    }
}

$stmt = $db->prepare("SELECT COUNT(*) FROM results WHERE user_id = ?");
$stmt->execute([$user['id']]);
$totalTaken = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT SUM(score) FROM results WHERE user_id = ?");
$stmt->execute([$user['id']]);
$totalPoints = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT r.score, r.date, q.titre FROM results r JOIN quiz q ON r.quiz_id = q.id WHERE r.user_id = ? ORDER BY r.date DESC");
$stmt->execute([$user['id']]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mon Profil - QuizApp</title>
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
    h3 {
      margin-top: 3rem;
    }
    .summary {
      margin-top: 1rem;
      line-height: 1.6;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1.5rem;
    }
    th, td {
      padding: 1rem;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background: #f0f0f0;
    }
    .btn {
      display: inline-block;
      margin-top: 2rem;
      background: #2d89ef;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.5rem;
      text-decoration: none;
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>
  <div class="container">
    <h2>Mon Profil</h2>
    <form method="POST">
      <div>
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
      </div>
      <div>
        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div>
        <label>Mot de passe actuel :</label>
        <input type="password" name="ancien_mot_de_passe" placeholder="Mot de passe actuel">
      </div>
      <div>
        <label>Nouveau mot de passe :</label>
        <input type="password" name="mot_de_passe" placeholder="••••••">
      </div>
      <button type="submit">Mettre à jour</button>
    </form>

    <?php if ($success): ?>
      <div class="success">✅ <?= $success ?></div>
    <?php endif; ?>

    <h3>Mes Résultats</h3>
    <div class="summary">
      <p>Total de quiz passés : <strong><?= $totalTaken ?></strong></p>
      <p>Total des points obtenus : <strong><?= $totalPoints ?? 0 ?></strong></p>
    </div>

    <?php if ($results): ?>
      <table>
        <thead>
          <tr>
            <th>Quiz</th>
            <th>Score</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['titre']) ?></td>
              <td><?= $r['score'] ?></td>
              <td><?= date("d/m/Y H:i", strtotime($r['date'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Aucun résultat enregistré.</p>
    <?php endif; ?>

    
  </div>
</body>
</html>
