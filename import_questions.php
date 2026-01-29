<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';

Auth::startSession();
if (!Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // skip header line

        $stmt = $db->prepare("INSERT INTO questions (quiz_id, question_text, bonne_reponse, mauvaises_reponses) VALUES (:quiz_id, :question_text, :bonne_reponse, :mauvaises_reponses)");

        $rowCount = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $stmt->execute([
                ':quiz_id' => $data[0],
                ':question_text' => $data[1],
                ':bonne_reponse' => $data[2],
                ':mauvaises_reponses' => $data[3]
            ]);
            $rowCount++;
        }

        fclose($handle);
        $message = "✅ $rowCount question(s) importée(s) avec succès.";
    } else {
        $message = "❌ Impossible d’ouvrir le fichier.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Importer des Questions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; margin: 0; padding: 2rem; }
    .container { background: white; padding: 2rem; border-radius: 1rem; max-width: 600px; margin: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 1.5rem; }
    input[type="file"] { margin-bottom: 1rem; }
    button { padding: 0.75rem 1.25rem; background: #2d89ef; color: white; border: none; border-radius: 0.5rem; cursor: pointer; }
    p.message { margin-top: 1rem; color: green; }
  </style>
</head>
<body>
  <div class="container">
    <h2>📥 Importer des Questions (CSV)</h2>

    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="csv_file" accept=".csv" required><br>
      <button type="submit">Importer</button>
    </form>

    <?php if ($message): ?>
      <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <p>📌 Format attendu du fichier CSV :</p>
    <pre>quiz_id,question_text,bonne_reponse,mauvaises_reponses</pre>
    <pre>1,Quelle est la capitale de la France ?,Paris,"Lyon,Marseille,Nice"</pre>
  </div>
</body>
</html>
