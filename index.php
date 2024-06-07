<?php

require "vendor/autoload.php";

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host=$_ENV["DB_HOST"];
$db=$_ENV["DB_NAME"];
$user=$_ENV["DB_USER"];
$pass=$_ENV["DB_PASS"];
$charset="utf8mb4";

$dsn="mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];


try {
  $pdo = new PDO($dsn, $user, $pass, $options);

} catch (\PDOException $e) {
  throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST["title"])) {
    $stmt = $pdo->prepare('INSERT INTO tasks (title) VALUES (?)');
    $stmt->execute([$_POST['title']]);
  } elseif (isset($_POST["completed"])) {
    $stmt = $pdo->prepare("UPDATE tasks SET completed = 1 WHERE id = ?");
    $stmt->execute([$_POST["completed"]]);
  }
}

$tasks = $pdo->query("SELECT * FROM tasks")->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>My todo</title>
</head>
<body>
  <div class="container">
        <h1>To-Do List</h1>
        <form method="post">
          <labe for="title">Todo:</labe>
            <input type="text" name="title" placeholder="New task" required>
            <button type="submit">Add</button>
        </form>
        <ul>
          <!--  -->
            <?php foreach ($tasks as $task): ?>
                <li>
                    <?php if ($task['completed']): ?>
                        <strike><?= htmlspecialchars($task['title']) ?></strike>
                    <?php else: ?>
                        <?= htmlspecialchars($task['title']) ?>
                        <form method="post" style="display:inline;">
                            <button type="submit" name="completed" value="<?= $task['id'] ?>">Complete</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>