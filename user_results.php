<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';

Auth::startSession();
if (!Auth::isLoggedIn() || Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$user_id = Auth::getUserId();
$db = (new Database())->connect();

// Fetch all results for this user
$stmt = $db->prepare("
    SELECT r.score, r.date, q.titre
    FROM results r
    JOIN quiz q ON r.quiz_id = q.id
    WHERE r.user_id = ?
    ORDER BY r.date DESC
");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total quizzes
$totalQuizzes = count($results);

// Total score
$totalScore = array_sum(array_column($results, 'score'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes Résultats</title>
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
      max-width: 800px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 1rem;
      color: #2c3e50;
    }
    .summary {
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
      color: #444;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th {
      background: #f0f0f0;
    }
  </style>
</head>
<body>

<?php include 'header_user.php'; ?>

<div class="container">
  <h2>Mes Résultats de Quiz</h2>

  <div class="summary">
    <p><strong>Total des quiz complétés :</strong> <?= $totalQuizzes ?></p>
    <p><strong>Total des points obtenus :</strong> <?= $totalScore ?></p>
  </div>

  <?php if ($totalQuizzes > 0): ?>
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
    <p>Vous n'avez pas encore de résultats enregistrés.</p>
  <?php endif; ?>
</div>

</body>
</html>
