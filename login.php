<?php
    // database connection
    require('dbconnect.php'); 
    session_start();
    $pageTitle = "Log in";
//sign up validation
    $msg   = "";
    $error ='';
$msgSignup = '';
//$decryptingPassword = '';
    if (isset($_POST['signUp'])) {
        $username        = trim($_POST['username']);
        $email           = trim($_POST['email']);
        $password        = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);
    try {
	$stmt  = $dbconnect->query("SELECT * FROM users");
	$users = $stmt->fetchAll(); 
   } catch (\PDOException $e) {
	throw new \PDOException($e->getMessage(), (int) $e->getCode());
  }
    //check database for existing email
    foreach ($users as $key => $user) { 
    if($email === $user['email']){
     $error .= '<li> Email already exists.</li>';
    }
    }   
    //validation of username
  if (empty($username)) {
       $error .=  '<li> username must not be empty</li>';
  }else if(is_numeric($username)){
       $error .=  '<li> username: Only number is not allowed</li>';
  }else if(!preg_match("/^[a-zA-Z ]*$/",$username)){
       $error .=  '<li> username: Only letter and whitespace are allowed</li>'; 
  }else if(strlen($username) > 30){
       $error .=  '<li> Title must have less than 30 characters</li>'; 
  }else if(strlen($username) < 5){
       $error .=  '<li> Title must be 4 characters long</li>'; 
  }
    //validation of email           
    if(empty($email)){
        $error .= '<li>Email must not be empty</li>';
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error .= '<li>Incorrect email format</li>';
        //if want to allow &#?+!% just place after a-z0-9
        //but not allow /\)(
    } else if(!preg_match('/^([a-z0-9_\.-]+)@([a-z\.-]+)\.([a-z\.]{2,6})$/',$email)){
        $error .= '<li>Not good formatted email, use lowercase,number and _-. only';   
    }else if(preg_match('/^([0-9]+)@([a-z\.-]+)\.([a-z\.]{2,6})$/',$email)){
        $error .= '<li>Only number is not allowed</li>';
    } 
    //validation of password 
    if(empty($password)){
        $error .= '<li>Password must not be empty</li>';
    }
    if(!empty($password) && strlen($password) < 6){
        $error .= '<li>Password must have at least 6 character</li>';
    }
    if($confirmPassword !== $password){
        $error .= '<li>Confirm password must be same as password</li>';
    }
    //encrypting password
    $secrectPassword = password_hash($password,PASSWORD_BCRYPT);
    if($error){
        $msgSignup = "<ul style='background-color:#f8d7da;'>{$error}</ul>";
    }else{
        //after validation data inserted into table
    try {
      $query = "
        INSERT INTO users (username, password, email)
        VALUES (:username, :password, :email);
      ";
      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':password', $secrectPassword);
      $stmt->bindValue(':email', $email);
      $result = $stmt->execute();    
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
    if($result){
      $msgSignup = "<ul style='background-color:#d4edda;'>Sign up successfull. Now you can log in with email and password</ul>";  
    }
    }
}
    //Log in validation
    if (isset($_POST['doLogin'])) {
        $email    = trim($_POST['email']);
        $password = trim($_POST['password']);
    try {
      $query = "
        SELECT * FROM users 
        WHERE email = :email;
      ";
      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':email', $email);
//      $stmt->bindValue(':password', $decryptingPassword);
      $stmt->execute(); 
      $user = $stmt->fetch(); 
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    } 
    //validation of email           
    if(empty($email)){
         $error .=  '<li> Email must not be empty</li>';
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
         $error .=  '<li> Incorrect email format</li>';
    }else if ($email !== $user['email']){
         $error .=  '<li>'. $email. ' is not registered</li>';
    }
      //decrypting password
      $decryptPassword = password_verify($password,$user['password']);  
    if(empty($password)){
         $error .=  '<li> Password must not be empty</li>';
    }else if($email === $user['email'] && !$decryptPassword){
         $error .=  '<li>' .$password. ' is not correct password</li>';
    }
    if($user['email'] && $decryptPassword){
            $_SESSION['username'] = $user['username'];
            header('Location: admin.php');
            exit;
    }
    if($error){
        $msg = "<ul style='background-color:#f8d7da;'>{$error}</ul>";
    }
    }
?>
<?php include('head.php'); ?>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12" id="bkgdImg">
            <img src="img/bookbackground.jpg" alt="background img">
            </div>
            <div class="col-4">
                <form method="POST" action="#">
                    <legend class="text-left mt-3">Log in</legend>
                    <hr>
                    <!--show error message for log in-->
                    <?=$msg?><br>
                    <p>
                        <label for="input1"> E-mail:</label><br>
                        <input type="text" class="form-control" name="email">
                    </p>
                    <p>
                        <label for="input2">Password:</label><br>
                        <input type="password" class="form-control" name="password">
                    </p>
                    <p>
                        <input type="submit" name="doLogin" value="Login" class="btn btn-success">
                    </p>
                </form>
                <hr>
            </div>
            <div class="offset-3 col-4">
                <form method="POST" action="#">
                    <legend class="text-left mt-3">Sign up</legend>
                    <hr>
                    <!--show error message for Sign Up-->
                    <?=$msgSignup?>
                    <p>
                        <label for="input1">User name:</label><br>
                        <input type="text" class="form-control" name="username">
                    </p>
                    <p>
                        <label for="input2">E-mail:</label><br>
                        <input type="text" class="form-control" name="email">
                    </p>
                    <p>
                        <label for="input3">Password:</label><br>
                        <input type="password" class="form-control" name="password">
                    </p>
                    <p>
                        <label for="input4">Confirm Password:</label><br>
                        <input type="password" class="form-control" name="confirmPassword">
                    </p>
                    <p>
                        <input type="submit" name="signUp" value="Sign Up" class="btn btn-success">
                    </p>
                </form>
                <hr>
            </div>
        </div>
    </div>
    <?php include('bootstrap.php'); ?>
    <?php include('footer.php'); ?>
