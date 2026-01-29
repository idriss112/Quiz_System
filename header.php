<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

Auth::startSession();
if (!Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$userObj = new User($db);
$admin = $userObj->getUserById(Auth::getUserId());
?>
<header style="background-color: #2d89ef; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center;">
  <div class="logo" style="font-size: 1.25rem; font-weight: 600;">QuizApp</div>
  <nav>
    <a href="admin_dashboard.php" style="color: white; margin-left: 1rem; text-decoration: none;">Dashboard</a>
    <a href="admin_manage_quiz.php" style="color: white; margin-left: 1rem; text-decoration: none;">Gérer les quiz</a>
    <a href="admin_manage_questions.php" style="color: white; margin-left: 1rem; text-decoration: none;">Gérer les questions</a>
    <a href="admin_results.php" style="color: white; margin-left: 1rem; text-decoration: none;">Gérer les resultas</a>
    <a href="admin_manage_users.php" style="color: white; margin-left: 1rem; text-decoration: none;">Gérer les utilisateurs</a>
    <a href="profile.php" style="color: white; margin-left: 1rem; text-decoration: none;">Profil</a>
    <a href="logout.php" style="color: white; margin-left: 1rem; text-decoration: none;">Déconnexion</a>
  </nav>
</header>
