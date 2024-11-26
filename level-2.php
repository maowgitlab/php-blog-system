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
    header('Location: level-2.php');
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Simple Blog</h1>
        <form id="post-form" action="" method="post" class="bg-white p-4 rounded shadow">
            <input type="hidden" name="id" id="post-id">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-bold mb-2">Title:</label>
                <input type="text" name="title" id="title" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="content" class="block text-gray-700 font-bold mb-2">Content:</label>
                <textarea name="content" id="content" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <div>
                <button type="submit" name="create" id="create-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create</button>
                <button type="submit" name="edit" id="edit-btn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" style="display:none;">Edit</button>
            </div>
        </form>

        <h2 class="text-xl font-bold mt-8 mb-4">Posts</h2>
        <ul class="space-y-4">
            <?php foreach ($posts as $post): ?>
                <li class="bg-white p-4 rounded shadow">
                    <h3 class="text-lg font-bold"><?= htmlspecialchars($post['title']); ?></h3>
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($post['content'])); ?></p>
                    <div class="mt-2">
                        <button onclick="editPost('<?= $post['id']; ?>', '<?= addslashes($post['title']); ?>', '<?= addslashes($post['content']); ?>')" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">Edit</button>
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $post['id']; ?>">
                            <button type="submit" name="delete" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script>
    function editPost(id, title, content) {
        document.getElementById('post-id').value = id;
        document.getElementById('title').value = title;
        document.getElementById('content').value = content;
        document.getElementById('create-btn').style.display = 'none';
        document.getElementById('edit-btn').style.display = 'inline-block';
    }
    </script>
</body>
</html>
