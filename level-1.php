<?php
// Function to read all posts from the JSON file
function readPosts() {
    $json = file_get_contents('posts.json');
    return json_decode($json, true);
}

// Function to write posts to the JSON file
function writePosts($posts) {
    $json = json_encode($posts, JSON_PRETTY_PRINT);
    file_put_contents('posts.json', $json);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $posts = readPosts();

    if (isset($_POST['create'])) {
        $newPost = [
            'id' => uniqid(),
            'title' => $_POST['title'],
            'content' => $_POST['content']
        ];
        $posts[] = $newPost;
    } elseif (isset($_POST['edit'])) {
        foreach ($posts as &$post) {
            if ($post['id'] == $_POST['id']) {
                $post['title'] = $_POST['title'];
                $post['content'] = $_POST['content'];
                break;
            }
        }
    } elseif (isset($_POST['delete'])) {
        $posts = array_filter($posts, function($post) {
            return $post['id'] != $_POST['id'];
        });
    }

    writePosts($posts);
    header('Location: level-1.php');
    exit;
}

// Read all posts for displaying
$posts = readPosts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Blog</title>
</head>
<body>
    <h1>Simple Blog</h1>
    <form id="post-form" action="" method="post">
        <input type="hidden" name="id" id="post-id">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        <br>
        <label for="content">Content:</label>
        <textarea name="content" id="content" required></textarea>
        <br>
        <button type="submit" name="create" id="create-btn">Create</button>
        <button type="submit" name="edit" id="edit-btn" style="display:none;">Edit</button>
    </form>

    <h2>Posts</h2>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <h3><?= htmlspecialchars($post['title']); ?></h3>
                <p><?= nl2br(htmlspecialchars($post['content'])); ?></p>
                <button onclick="editPost('<?= $post['id']; ?>', '<?php echo addslashes($post['title']); ?>', '<?= addslashes($post['content']); ?>')">Edit</button>
                <form action="" method="post" style="display: inline;">
                    <input type="hidden" name="id" value="<?= $post['id']; ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <script>
    function editPost(id, title, content) {
        document.getElementById('post-id').value = id;
        document.getElementById('title').value = title;
        document.getElementById('content').value = content;
        document.getElementById('create-btn').style.display = 'none';
        document.getElementById('edit-btn').style.display = 'inline';
    }
    </script>
</body>
</html>
