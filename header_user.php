<?php
require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

Auth::startSession();
if (!Auth::isLoggedIn() || Auth::isAdmin()) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->connect();
$userObj = new User($db);
$user = $userObj->getUserById(Auth::getUserId());
?>
<header style="background-color: #2d89ef; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center;">
  <div class="logo" style="font-size: 1.25rem; font-weight: 600;">QuizApp</div>
  <nav>
    <a href="dashboard.php" style="color: white; margin-left: 1rem; text-decoration: none;">Quiz</a>
    <a href="profile-user.php" style="color: white; margin-left: 1rem; text-decoration: none;">Profil</a>
    <a href="logout.php" style="color: white; margin-left: 1rem; text-decoration: none;">Déconnexion</a>
  </nav>
</header>
