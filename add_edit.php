<<?php
require_once "../app/Database.php";

use App\Database;

$database = new Database();

$title = "Simple PHP CRUD - Add / Edit Contacts";

if ($_POST) {
    $id = $_POST['id'] ?? null;
    $name_first = $_POST['name_first'];
    $name_last = $_POST['name_last'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];

    if ($id) {
        $database->updateContact($id, $name_first, $name_last, $email, $birthday);
    } else {
        $database->addContact($name_first, $name_last, $email, $birthday);
    }

    header("Location: index.php");
}

$id = $_GET['id'] ?? null;
if ($id) {
    $contact = $database->getContact($id);
} else {
    $contact = [];
}
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <title><?php echo $title ?></title>
    </head>

    <body class="container">
        <h1><?php echo $title ?></h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name_first" class="form-label">First Name</label>
                <input type="text" class="form-control" id="name_first" name="name_first" placeholder="First Name"
                    value="<?php echo $contact['name_first'] ?? null; ?>">
            </div>
            <div class="mb-3">
                <label for="name_last" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="name_last" name="name_last" placeholder="Last Name"
                    value="<?php echo $contact['name_last'] ?? null; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                    value="<?php echo $contact['email'] ?? null; ?>">
            </div>
            <div class="mb-3">
                <label for="birthday" class="form-label">Birthday</label>
                <input type="date" class="form-control" id="birthday" name="birthday" placeholder="Birthday"
                    value="<?php echo $contact['birthday'] ?? null; ?>">
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </body>

    </html>