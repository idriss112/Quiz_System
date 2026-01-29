<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Quiz.php';

Auth::startSession();
if (!Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$quiz = new Quiz($db);

$editQuizId = $_GET['edit'] ?? null;

if (isset($_GET['delete'])) {
    $quiz->delete($_GET['delete']);
    header("Location: admin_manage_quiz.php");
    exit();
}

if (isset($_POST['save_quiz'])) {
    $quiz->update($_POST['quiz_id'], $_POST['titre'], $_POST['description']);
    header("Location: admin_manage_quiz.php");
    exit();
}

if (isset($_POST['add_quiz'])) {
    $quiz->create($_POST['titre'], $_POST['description']);
    header("Location: admin_manage_quiz.php");
    exit();
}

$allQuizzes = $quiz->getAll();
$edit_quiz = $editQuizId ? $quiz->getById($editQuizId) : null;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 4;
$total = count($allQuizzes);
$totalPages = ceil($total / $perPage);
$offset = ($page - 1) * $perPage;
$quizzes = array_slice($allQuizzes, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Quiz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; margin: 0; }
    .container { max-width: 1000px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 1rem; }
    .btn-add { margin-bottom: 1.5rem; padding: 0.6rem 1.2rem; background-color: #2d89ef; color: white; border: none; border-radius: 0.5rem; cursor: pointer; }
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { padding: 1rem; border-bottom: 1px solid #ddd; text-align: left; vertical-align: top; }
    .actions a { padding: 0.4rem 0.8rem; text-decoration: none; color: white; border-radius: 0.3rem; margin-right: 0.3rem; display: inline-block; }
    .edit { background-color: #2d89ef; }
    .delete { background-color: #e74c3c; }
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); justify-content: center; align-items: center; z-index: 1000; }
    .modal-content { background: white; padding: 2rem; border-radius: 1rem; max-width: 500px; width: 90%; box-shadow: 0 0 20px rgba(0,0,0,0.15); }
    .modal-content input, .modal-content textarea { width: 100%; padding: 0.75rem; margin-top: 0.5rem; border: 1px solid #ccc; border-radius: 0.5rem; font-size: 1rem; }
    .modal-content button { margin-top: 1rem; padding: 0.75rem; border: none; border-radius: 0.5rem; background-color: #2d89ef; color: white; cursor: pointer; font-size: 1rem; }
    .modal-content button.cancel { background-color: #ccc; color: #333; margin-left: 0.5rem; }
    .pagination { margin-top: 1rem; text-align: center; }
    .pagination a { padding: 0.5rem 0.75rem; margin: 0 0.25rem; border-radius: 0.5rem; border: 1px solid #ccc; text-decoration: none; color: #2d89ef; }
    .pagination a.active { background: #2d89ef; color: white; border-color: #2d89ef; }
  </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container">
  <h2>Gestion des Quiz</h2>
  <button class="btn-add" onclick="document.getElementById('modalAdd').style.display='flex'">Ajouter un Quiz</button>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Description</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($quizzes as $q): ?>
        <tr>
          <td><?= htmlspecialchars($q['titre']) ?></td>
          <td><?= htmlspecialchars($q['description']) ?></td>
          <td><?= $q['date_creation'] ?></td>
          <td class="actions">
            <a class="edit" href="?edit=<?= $q['id'] ?>">Modifier</a>
            <a class="delete" href="?delete=<?= $q['id'] ?>" onclick="return confirm('Supprimer ce quiz ?')">Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>" class="<?= $page === $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>

  <div class="modal" id="modalAdd">
    <div class="modal-content">
      <form method="POST">
        <label>Titre :</label>
        <input type="text" name="titre" required>
        <label>Description :</label>
        <textarea name="description" required></textarea>
        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
          <button type="submit" name="add_quiz">Ajouter</button>
          <button type="button" class="cancel" onclick="document.getElementById('modalAdd').style.display='none'">Annuler</button>
        </div>
      </form>
    </div>
  </div>

  <?php if ($edit_quiz): ?>
    <div class="modal" id="modalEdit" style="display:flex;">
      <div class="modal-content">
        <form method="POST">
          <input type="hidden" name="quiz_id" value="<?= $edit_quiz['id'] ?>">
          <label>Titre :</label>
          <input type="text" name="titre" value="<?= htmlspecialchars($edit_quiz['titre']) ?>" required>
          <label>Description :</label>
          <textarea name="description" required><?= htmlspecialchars($edit_quiz['description']) ?></textarea>
          <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
            <button type="submit" name="save_quiz">Enregistrer</button>
            <a href="admin_manage_quiz.php"><button type="button" class="cancel">Annuler</button></a>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
</div>
<script>
  window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(m => { if (event.target === m) m.style.display = 'none'; });
  };
</script>
</body>
</html>