<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Quiz.php';
require_once 'classes/User.php';

Auth::startSession();
if (!Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$quiz = new Quiz($db);
$user = new User($db);

$quizzes = $quiz->getAll();
$users = $user->getAll();

// Pagination
$limit = 7;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total results with filters
$countSql = "SELECT COUNT(*) FROM results r WHERE 1";
$countParams = [];

if (!empty($_GET['quiz_id'])) {
    $countSql .= " AND r.quiz_id = :quiz_id";
    $countParams[':quiz_id'] = $_GET['quiz_id'];
}
if (!empty($_GET['user_id'])) {
    $countSql .= " AND r.user_id = :user_id";
    $countParams[':user_id'] = $_GET['user_id'];
}

$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$totalResults = $countStmt->fetchColumn();
$totalPages = ceil($totalResults / $limit);

// Build main query
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

$sql .= " ORDER BY r.date DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);

// Bind filters
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

// Bind pagination
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Résultats des Quiz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
    .container { max-width: 1100px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 2rem; }
    form.filters {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    form select, form button {
      padding: 0.75rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
    }
    form button {
      background-color: #2d89ef;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }
    form button:hover {
      background-color: #226bba;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td {
      padding: 1rem;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background: #f0f0f0;
    }
    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }
      th { position: absolute; left: -9999px; }
      td {
        position: relative;
        padding-left: 50%;
        margin-bottom: 1rem;
      }
      td::before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        font-weight: bold;
      }
    }
    .pagination {
      text-align: center;
      margin-top: 2rem;
    }
    .pagination a {
      margin: 0 5px;
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 5px;
      text-decoration: none;
      color: #333;
    }
    .pagination a.active {
      background-color: #2d89ef;
      color: white;
      border-color: #2d89ef;
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>
  <div class="container">
    <h2>Résultats des Quiz</h2>

    <form method="GET" class="filters">
      <select name="quiz_id">
        <option value="">-- Tous les quiz --</option>
        <?php foreach ($quizzes as $q): ?>
          <option value="<?= $q['id'] ?>" <?= (isset($_GET['quiz_id']) && $_GET['quiz_id'] == $q['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($q['titre']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="user_id">
        <option value="">-- Tous les utilisateurs --</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>" <?= (isset($_GET['user_id']) && $_GET['user_id'] == $u['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nom']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit">Filtrer</button>
    </form>
    <a href="export_results.php?<?= http_build_query($_GET) ?>"
   style="display:inline-block;margin-bottom:1rem;padding:0.75rem 1.25rem;background:#2d89ef;color:white;text-decoration:none;border-radius:8px;">
    Exporter les résultats en CSV
    </a>
    <?php if ($results): ?>
    <table>
      <thead>
        <tr>
          <th>Utilisateur</th>
          <th>Quiz</th>
          <th>Score</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $row): ?>
        <tr>
          <td data-label="Utilisateur"><?= htmlspecialchars($row['user_nom']) ?></td>
          <td data-label="Quiz"><?= htmlspecialchars($row['quiz_titre']) ?></td>
          <td data-label="Score"><?= $row['score'] ?></td>
          <td data-label="Date"><?= $row['date'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>Aucun résultat trouvé.</p>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
           class="<?= $i == $page ? 'active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</body>
</html>
