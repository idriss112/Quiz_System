<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';

Auth::startSession();
if (!Auth::isLoggedIn() || Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) {
    echo "Aucun quiz sélectionné.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Quiz</title>
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
      padding: 2rem;
      max-width: 900px;
      margin: auto;
    }
    .question-box {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 700px;
      margin: 0 auto;
      display: none;
    }
    .question-box.active {
      display: block;
    }
    .answers label {
      display: block;
      margin: 1rem 0;
    }
    button {
      padding: 0.7rem 1.2rem;
      background-color: #2d89ef;
      color: white;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      margin-top: 1rem;
    }
    #result {
      text-align: center;
      font-size: 1.5rem;
      font-weight: bold;
      display: none;
      margin-top: 2rem;
    }
  </style>
</head>
<body>

<?php include 'header_user.php'; ?>

<div class="container">
  <div class="question-box" id="quizBox">
    <h2 id="questionText"></h2>
    <div class="answers" id="answersContainer"></div>
    <button onclick="submitAnswer()">Suivant</button>
  </div>

  <div id="result"></div>
</div>

<script>
  const QUIZ_ID = <?= (int)$quiz_id ?>;
</script>
<script src="js/quiz.js"></script>

<script>
  // 🚨 Prevent accidental navigation during quiz
  let quizInProgress = true;

  window.addEventListener('beforeunload', function (e) {
    if (quizInProgress) {
      const confirmationMessage = "Êtes-vous sûr de vouloir quitter ce quiz ? Votre progression sera perdue.";
      e.preventDefault();
      e.returnValue = confirmationMessage;
      return confirmationMessage;
    }
  });

  // 🛑 Disable warning when quiz is finished
  function finishQuiz() {
    quizInProgress = false;
  }

  // 🚨 Intercept link clicks during quiz
  document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", function (e) {
      if (quizInProgress) {
        const confirmLeave = confirm("Êtes-vous sûr de vouloir quitter ce quiz ? Votre progression sera perdue.");
        if (!confirmLeave) {
          e.preventDefault();
        }
      }
    });
  });
</script>

</body>
</html>
