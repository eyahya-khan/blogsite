<?php
//database connection
require('dbconnect.php');
$pageTitle = 'Administration page';
session_start();
//check username and password have value or not
if(isset($_SESSION['username'])){
    $loginUsername = $_SESSION['username'];
}else{
    header('Location: login.php');
}
//remove username and password
if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}
// Fetch all posts to display on page
try {
  $query = "SELECT * FROM posts;";
  $stmt = $dbconnect->query($query);
  $posts = $stmt->fetchAll();
} catch (\PDOException $e) {
  throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>
<?php include('head.php'); ?>
<body>
    <div class="container">
        <div class="row">
            <div class="offset-1 col-10">
                <form action="" method="POST">
                    <div class="input-group-append mt-3 d-flex justify-content-end">
                        <!--display user name-->
                        <label class="mt-2 mr-2"><?php echo 'Welcome '.ucfirst($loginUsername); ?></label>
                        <input type="submit" name="logout" value="Log out" class="btn btn-outline-dark border-info">
                    </div>
                </form>
                <h1>Blog Administration</h1>
                <div id="form-message"><?=$message?></div>
                <form action="add.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="title" class="form-control border-success" placeholder="Blog tilte">
                        <input type="text" name="author" class="form-control border-success" placeholder="Author name"><br>
                    </div>
                    <textarea name="content" class="form-control border-success" placeholder="Blog content" rows="5" cols="30"></textarea>
                    <div class="input-group-append mt-3 mr-2 float-right">
                        <input type="submit" name="addBtn" value="Add" class="btn btn-success" id="add-product-btn">
                    </div>
                </form><br><br>
            <!--search-->
            <div class="form-group">
            <input type="text" class="form-control" name="searchQuery" id="search-input" placeholder="Search">
            </div>
                <h3>All posts list at a glance</h3>
                <hr>
                <ul id="post-list" class="list-group">
                    <?php foreach ($posts as $key => $pun) { ?>
                    <li class="list-group-item border-info mb-1">
                        <!--add post-->
                        <p>
                        <h3><?=htmlentities($pun['title'])?></h3>
                        <?=htmlentities($pun['content'])?>
                        <h4><?=htmlentities($pun['author'])?></h4>
                        <?=htmlentities($pun['published_date'])?>
                        </p>
                        <!--Delete post-->
                        <form action="" method="POST" class="float-right">
                            <input type="hidden" name="hidId" value="<?=$pun['id']?>">
                            <input type="submit" name="deleteBtn" value="Delete" class="btn btn-danger delete-pun-btn">
                        </form>
                        <!--Update post-->
                        <button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#exampleModal" data-title="<?=htmlentities($pun['title'])?>" data-author="<?=htmlentities($pun['author'])?>" data-content="<?=htmlentities($pun['content'])?>" data-id="<?=htmlentities($pun['id'])?>">Update</button>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <!--update modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-info">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Update Blog</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Update Title: </label>
                            <input type="text" class="form-control" name="title" for="recipient-name">
                            <label for="recipient-name" class="col-form-label">Update content: </label>
                            <textarea class="form-control" name="content" for="recipient-name" rows="6"></textarea>
                            <label for="recipient-name" class="col-form-label">Update author: </label>
                            <input type="text" class="form-control" name="author" for="recipient-name">
                            <input type="hidden" class="form-control" name="id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" name="updateBtn" value="Update" class="btn btn-success update-pun-btn">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include('bootstrap.php'); ?>
    <!--
    <script>
        $('#exampleModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var title = button.data('title'); // Extract info from data-* attributes
            var content = button.data('content'); // Extract info from data-* attributes
            var author = button.data('author'); // Extract info from data-* attributes
            var id = button.data('id'); // Extract info from data-* attributes
            var modal = $(this);
            modal.find(".modal-body input[name='title']").val(title);
            modal.find(".modal-body textarea[name='content']").val(content);
            modal.find(".modal-body input[name='author']").val(author);
            modal.find(".modal-body input[name='id']").val(id);
        });
    </script>
-->
    <!--spell checker in content field-->
    <!--
    <script src="https://cdn.tiny.cloud/1/b1y2db7exjdaipku016s8wrj1xgiruek4eovrkb1cx0oeuyg/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script>tinymce.init({
	selector: 'textarea',
	plugins: 'tinymcespellchecker',
    forced_root_block: false, //remove p tag
	spellchecker_language: 'en'
});
</script>
-->
    <?php include('footer.php'); ?>
