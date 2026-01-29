<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Quiz.php';
require_once 'classes/Question.php';

Auth::startSession();
if (!Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$quiz = new Quiz($db);
$question = new Question($db);
$quizzes = $quiz->getAll();

$quiz_id = $_POST['quiz_id'] ?? $_GET['quiz_id'] ?? null;
$edit_id = $_GET['edit'] ?? null;
$message = '';

if (isset($_GET['delete'])) {
    $question->delete($_GET['delete']);
    header("Location: admin_manage_questions.php?quiz_id=" . $_GET['quiz_id']);
    exit();
}

if (isset($_POST['save_question'])) {
    $qid = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $bonne_reponse = $_POST['bonne_reponse'];
    $mauvaises_reponses = $_POST['mauvaises_reponses'];
    $image_url = $_POST['existing_image'] ?? null;

    if (!empty($_FILES['image']['name'])) {
        $target = "uploads/" . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_url = $target;
        }
    }

    $question->update($qid, $question_text, $bonne_reponse, $mauvaises_reponses, $image_url);
    header("Location: admin_manage_questions.php?quiz_id=$quiz_id");
    exit();
}

if (isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'];
    $bonne_reponse = $_POST['bonne_reponse'];
    $mauvaises_reponses = $_POST['mauvaises_reponses'];
    $image_url = null;

    if (!empty($_FILES['image']['name'])) {
        $target = "uploads/" . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_url = $target;
        }
    }

    $question->add($quiz_id, $question_text, $bonne_reponse, $mauvaises_reponses, $image_url);
    header("Location: admin_manage_questions.php?quiz_id=$quiz_id");
    exit();
}

// Import CSV
if (isset($_POST['import_csv']) && isset($_FILES['csv_file']) && $quiz_id) {
    $file = $_FILES['csv_file']['tmp_name'];
    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // Skip header
        $stmt = $db->prepare("INSERT INTO questions (quiz_id, question_text, bonne_reponse, mauvaises_reponses) VALUES (:quiz_id, :question_text, :bonne_reponse, :mauvaises_reponses)");
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $stmt->execute([
                ':quiz_id' => $quiz_id,
                ':question_text' => $data[0],
                ':bonne_reponse' => $data[1],
                ':mauvaises_reponses' => $data[2]
            ]);
        }
        fclose($handle);
        $message = "✅ Import terminé avec succès.";
    } else {
        $message = "❌ Échec de lecture du fichier.";
    }
}

$questions = $quiz_id ? $question->getByQuizId($quiz_id) : [];
$page = $_GET['page'] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$total = count($questions);
$totalPages = ceil($total / $limit);
$paginatedQuestions = array_slice($questions, $offset, $limit);
$edit_question = $edit_id ? $question->getById($edit_id) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Questions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      background: #f4f6f9;
    }
    .container {
      max-width: 1000px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    label {
      font-weight: 600;
      margin-bottom: 0.5rem;
      display: block;
    }
    select {
      width: 100%;
      padding: 0.75rem;
      font-size: 1rem;
      border-radius: 0.5rem;
      border: 1px solid #ccc;
      margin-bottom: 1rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      overflow-x: auto;
      display: block;
    }
    th, td {
      padding: 0.8rem;
      border-bottom: 1px solid #ddd;
      text-align: left;
      vertical-align: top;
    }
    .actions {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    .edit, .delete {
      padding: 0.4rem 0.8rem;
      text-align: center;
      color: white;
      border: none;
      border-radius: 0.3rem;
      text-decoration: none;
    }
    .edit { background-color: #2d89ef; }
    .delete { background-color: #e74c3c; }
    .btn-add {
      padding: 0.6rem 1.2rem;
      background-color: #2d89ef;
      color: white;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
    }
    .pagination {
      margin-top: 1.5rem;
      text-align: center;
    }
    .pagination a {
      margin: 0 0.25rem;
      padding: 0.5rem 0.75rem;
      border: 1px solid #ccc;
      border-radius: 5px;
      text-decoration: none;
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
  <h2>Gestion des Questions</h2>
  <form method="POST">
    <label for="quiz_id">Choisir un quiz :</label>
    <select name="quiz_id" id="quiz_id" onchange="this.form.submit()">
      <option value="">-- Sélectionner un quiz --</option>
      <?php foreach ($quizzes as $q): ?>
        <option value="<?= $q['id'] ?>" <?= ($q['id'] == $quiz_id) ? 'selected' : '' ?>>
          <?= htmlspecialchars($q['titre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>

  <?php if ($quiz_id): ?>
    <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
      <button class="btn-add" onclick="document.getElementById('modalAdd').style.display='flex'">Ajouter une Question</button>

      <form method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 0.5rem;">
        <input type="file" name="csv_file" accept=".csv" required style="
          padding: 0.6rem 1rem;
          border-radius: 0.5rem;
          border: 1px solid #ccc;
          background: #fff;
        ">
        <button type="submit" name="import_csv" style="
          background-color: #27ae60;
          color: white;
          padding: 0.6rem 1.2rem;
          border: none;
          border-radius: 0.5rem;
          cursor: pointer;
        ">📥 Importer CSV</button>
      </form>
    </div>

    <?php if ($message): ?>
      <p style="margin-top: 1rem; color: green; font-weight: 500;"><?= $message ?></p>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>Texte</th>
          <th>Bonne</th>
          <th>Mauvaises</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($paginatedQuestions as $q): ?>
          <tr>
            <td><?= htmlspecialchars($q['question_text']) ?></td>
            <td><?= htmlspecialchars($q['bonne_reponse']) ?></td>
            <td><?= htmlspecialchars($q['mauvaises_reponses']) ?></td>
            <td><?= $q['image_url'] ? '<img src="' . $q['image_url'] . '" style="max-width: 100px;">' : '-' ?></td>
            <td class="actions">
              <a class="edit" href="?quiz_id=<?= $quiz_id ?>&edit=<?= $q['id'] ?>">Modifier</a>
              <a class="delete" href="?quiz_id=<?= $quiz_id ?>&delete=<?= $q['id'] ?>" onclick="return confirm('Supprimer cette question ?')">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?quiz_id=<?= $quiz_id ?>&page=<?= $i ?>" class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
