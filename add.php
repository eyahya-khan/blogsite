<?php
//database connection
require('dbconnect.php');
// Add new post
$error = '';
$message = '';
if (isset($_POST['addBtn'])) {
//  $id      = trim($_POST['id']);
  $title   = trim($_POST['title']);
  $content = trim($_POST['content']);
  $author  = trim($_POST['author']);
    //validation of title
  if (empty($title)) {
      $error .=  '<li> Title must not be empty</li>';
  }else if(is_numeric($title)){
      $error .=  '<li> Title: number is not allowed</li>';
  }else if(!preg_match("/^[a-zA-Z ]*$/",$title)){
       $error .=  '<li> Title: Only letter and whitespace are allowed</li>'; 
  }else if(strlen($title) > 30){
       $error .=  '<li> Title must have less than 30 characters</li>'; 
  }else if(strlen($title) < 5){
       $error .=  '<li> Title must have 4 characters more</li>'; 
  }
    //validation of author
  if(empty($author)){
      $error .=  '<li> Author must not be empty</li>';
  }else if(is_numeric($author)){
      $error .=  '<li> Author: number is not allowed</li>';
  }else if(!preg_match("/^[a-zA-Z ]*$/",$author)){
       $error .=  '<li> Author: Only letter and whitespace are allowed</li>';
  }else if(strlen($author)> 30){
       $error .=  '<li> Author name must have less than 30 characters</li>';
  }else if(strlen($author) < 5){
       $error .=  '<li> Author name must have 4 character more</li>';
  }
    //validation of content
$firstWord=explode(' ',trim($_POST['content']));
  if(empty($content)){
      $error .=  '<li> Content must not be empty</li>';
  }else if(is_numeric($content)){
      $error .=  '<li> Content: number is not allowed</li>';
  }else if(!preg_match("/^[a-zA-Z]$/",$content[0])){
       $error .=  '<li>  Content: start with letter</li>';
  }else if(!preg_match("/^[a-zA-Z ]*$/", substr($content, 0, 15))){
       $error .=  '<li>  Content: First 15 character should have letter</li>';
  }else if(strlen($content) < 40){
       $error .=  '<li>  Content should have at least 40 character</li>';
  }else if(strlen($firstWord[0]) > 10){
       $error .=  '<li> First word is too long. *Use space to make it short</li>';
  }
  if($error){
       $message =  "<ul style='background-color:#f8d7da;'>{$error}</ul>";
    }
    else {
    try {
      $query = "
        INSERT INTO posts (title, content, author)
        VALUES (:title, :content, :author);
      ";
      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':content', $content);//
      $stmt->bindValue(':author', $author);
      $result =$stmt->execute();
    $message =  "<ul style='background-color:#d4edda;'>Your post uploaded successfully</ul>";
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
  }
}
//display all post
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