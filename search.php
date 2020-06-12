<?php
require('dbconnect.php');
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit;
$message = '';
// Update pun
if (isset($_POST['updateBtn'])) { 
  $pun = trim($_POST['pun']);
  if (empty($pun)) {
    $message = 
      '<div class="alert alert-danger" role="alert">
        Pun field must not be empty
      </div>';
  } else {
    try {
      $query = "
        UPDATE puns
        SET content = :pun
        WHERE id = :id;
      ";
      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':pun', $pun);
      $stmt->bindValue(':id', $_POST['id']);
      $stmt->execute();
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
  }
}
// Fetch puns to display on page
try {
  $query = "SELECT * FROM puns;";
  $stmt = $dbconnect->query($query);
  $puns = $stmt->fetchAll();
} catch (\PDOException $e) {
  throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
// output with JSON
$data = [
  'message' => $message,
  'puns'    => $puns,
];
echo json_encode($data);
