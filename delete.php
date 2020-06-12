<?php
//database connection
require('dbconnect.php');
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//exit;
// Delete post
$message = '';
if (isset($_POST['deleteBtn'])) {
  try {
    $query = "
      DELETE FROM posts
      WHERE id = :id;
    ";
    $stmt = $dbconnect->prepare($query);
    $stmt->bindValue(':id', $_POST['hidId']);
    $stmt->execute();
    $message = 
      '<div class="alert alert-success" role="alert">
        Post deleted successfully.
      </div>';
  } catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
  }
}
// Fetch puns to display on page
try {
  $query = "SELECT * FROM posts;";
  $stmt = $dbconnect->query($query);
  $posts = $stmt->fetchAll();
} catch (\PDOException $e) {
  throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
// output with JSON
$data = [
  'message' => $message,
  'posts'    => $posts,
];
echo json_encode($data);
?>