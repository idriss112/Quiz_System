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

if (isset($_GET['delete'])) {
    $userObj->delete($_GET['delete']);
    header("Location: admin_manage_users.php");
    exit();
}

if (isset($_POST['add_user'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $userObj->register($nom, $email, $password, $role);
    header("Location: admin_manage_users.php");
    exit();
}

if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $userObj->update($id, $nom, $email, $role);
    header("Location: admin_manage_users.php");
    exit();
}

$limit = 9;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;
$totalUsers = $userObj->countAll();
$totalPages = ceil($totalUsers / $limit);
$users = $userObj->getPaginated($limit, $offset);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des utilisateurs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
    .container { max-width: 1100px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 2rem; }
    table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
    th, td { padding: 1rem; border-bottom: 1px solid #eee; text-align: left; }
    th { background: #f0f0f0; }
    .actions a, .actions button {
      margin-right: 0.5rem;
      color: white;
      background: #2d89ef;
      padding: 0.5rem 1rem;
      border-radius: 0.4rem;
      text-decoration: none;
      border: none;
      cursor: pointer;
    }
    .actions .delete { background: #e74c3c; }
    .pagination { text-align: center; margin-top: 2rem; }
    .pagination a {
      margin: 0 0.25rem;
      padding: 0.5rem 0.75rem;
      text-decoration: none;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .pagination a.active {
      background-color: #2d89ef;
      color: white;
      border-color: #2d89ef;
    }
    form.add-user-form {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1rem;
      align-items: end;
      margin-bottom: 1rem;
    }
    form.add-user-form input,
    form.add-user-form select,
    form.add-user-form button {
      padding: 0.75rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
    }
    form.add-user-form button {
      background-color: #2d89ef;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }
    form.add-user-form button:hover {
      background-color: #226bba;
    }
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      max-width: 400px;
      width: 100%;
    }
    .modal-content input, .modal-content select {
      width: 100%;
      margin-bottom: 1rem;
      padding: 0.75rem;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
    }
    .modal-content button {
      padding: 0.75rem;
      background: #2d89ef;
      color: white;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      width: 100%;
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>
  <div class="container">
    <h2>Gestion des utilisateurs</h2>

    <form method="POST" class="add-user-form">
      <input type="text" name="nom" placeholder="Nom" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Mot de passe" required>
      <select name="role" required>
        <option value="participant">participant</option>
        <option value="admin">Administrateur</option>
      </select>
      <button type="submit" name="add_user">Ajouter</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Email</th>
          <th>Rôle</th>
          <th>Date d'inscription</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= htmlspecialchars($user['nom']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= $user['role'] ?></td>
          <td><?= $user['date_inscription'] ?></td>
          <td class="actions">
            <button type="button" onclick="openModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nom']) ?>', '<?= htmlspecialchars($user['email']) ?>', '<?= $user['role'] ?>')">Modifier</button>
            <a class="delete" href="?delete=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="id" id="editId">
        <input type="text" name="nom" id="editNom" required>
        <input type="email" name="email" id="editEmail" required>
        <select name="role" id="editRole" required>
          <option value="participant">participant</option>
          <option value="admin">Administrateur</option>
        </select>
        <button type="submit" name="update_user">Enregistrer</button>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById('editModal');
    function openModal(id, nom, email, role) {
      document.getElementById('editId').value = id;
      document.getElementById('editNom').value = nom;
      document.getElementById('editEmail').value = email;
      document.getElementById('editRole').value = role;
      modal.style.display = 'flex';
    }
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</body>
</html>
