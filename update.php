<?php
//database connection
require('dbconnect.php');
// Update blog
$error ='';
$message='';
if (isset($_POST['updateBtn'])) { 
  $title   = trim($_POST['title']);
  $content = trim($_POST['content']);
  $author  = trim($_POST['author']);
    //validation of title
  if (empty($title)) {
      $error .=  '<li> Update: Title must not be empty</li>';
  }else if(is_numeric($title)){
      $error .=  '<li> Update:  In title, number is not allowed</li>';
  }else if(!preg_match("/^[a-zA-Z ]*$/",$title)){
       $error .=  '<li> Update: In Title, only letter and whitespace are allowed</li>'; 
  }else if(strlen($title) > 30){
       $error .=  '<li> Update: Title must have less than 30 characters</li>'; 
  }else if(strlen($title) < 5){
       $error .=  '<li> Update: Title must have 4 characters more</li>'; 
  }
    //validation of author
  if(empty($author)){
      $error .=  '<li> Update: Author must not be empty</li>';
  }else if(is_numeric($author)){
      $error .=  '<li> Update: In author, number is not allowed</li>';
  }else if(!preg_match("/^[a-zA-Z ]*$/",$author)){
       $error .=  '<li> Update: In author, only letter and whitespace are allowed</li>';
  } else if(strlen($author)> 30){
       $error .=  '<li> Update: Author name must have less than 30 characters</li>';
  }else if(strlen($author) < 5){
       $error .=  '<li> Update: Author name must have 4 characters more</li>';
  }
    //validation of content
  if(empty($content)){
      $error .=  '<li> Update: Content must not be empty</li>';
  }else if(is_numeric($content)){
      $error .=  '<li> Update: In content, number is not allowed</li>';
  }else if(!preg_match("/^[a-zA-Z]$/",$content[0])){
       $error .=  '<li>  Update: In content, start with letter</li>';
  }else if(!preg_match("/^[a-zA-Z ]*$/",substr($content, 0, 15))){
       $error .=  '<li>  Update: In content, First 15 character should have letter</li>';
  }else if(strlen($content) < 40){
       $error .=  '<li> Update: Content should have at least 40 character</li>';
  }else if(strlen($content[0]) > 10){
       $error .=  '<li> First word is too long. *Use space to make it short</li>';
  }
  if($error){
       $message =  "<ul style='background-color:#f8d7da;'>{$error}</ul>";
    }
    else {
    try {
      $query = "
        UPDATE posts
        SET content = :content,title = :title,author = :author
        WHERE id = :id;
      ";
      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':content', $content);
      $stmt->bindValue(':author', $author);
      $stmt->bindValue(':id', $_POST['id']);
      $stmt->execute();
     $message =  "<ul style='background-color:#d4edda;'>Post updated successfully</ul>";
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