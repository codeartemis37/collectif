        <div class="confirmation-box">
            <a href="<?= $site ?>uploads/">Uploader des fichiers</a>
            <a href="<?= $site ?>uploads/indexfiles.php">Récupérer des fichiers</a>
            <a href="<?= $site ?>uploads/delete.php">Supprimer tous les fichiers</a>
        </div>
    <style>
h1, h3 {
    text-align: center;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #444;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
}

.confirmation-box, .file-list, .message, .file-item {
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.confirmation-box, .file-item {
    background-color: #555;
}

.file-list {
    max-height: 200px;
    overflow-y: auto;
    background-color: #666;
    color: #eee;
}

.file-list ul {
    list-style-type: none;
    padding: 0;
}

.file-list li {
    padding: 5px 0;
    border-bottom: 1px solid #777;
}

.file-list li:last-child {
    border-bottom: none;
}

button, .button, a, input[type="submit"] {
    border: none;
    color: white;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
    background-color: #e67e22;
}

button:hover, .button:hover, a:hover, input[type="submit"]:hover {
    background-color: #d35400;
}

button[name="cancel_delete"] {
    background-color: #c0392b;
}

.message {
    background-color: #555;
    text-align: center;
}

select, input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: 1px solid #666;
    background-color: #555;
    color: #fff;
    box-sizing: border-box;
}

.file-name {
    font-weight: bold;
    margin-bottom: 5px;
    color: #ccc;
}

.send-files {
    display: block;
    margin-top: 20px;
}

.form-group {
    margin: 15px 0;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #ccc;
}

input[type="submit"] {
    display: block;
    width: 100%;
    margin: 20px 0;
}
    </style>
