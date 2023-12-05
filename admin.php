<?php
session_start();

require('./utils/Auth.php');
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
if (!$isLoggedIn || $_SESSION['discriminator'] !== "admin") {
    header("Location: index.php");
    exit;
}

if ($_POST) {
    require('./processPost.php');
    if (isset($_POST['password'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && $_POST['username'] &&  $_POST['password']) {

            $result = handlePostFromAdmin();

            if ($result) {
                $_SESSION['create_success'] = 'Success! You can login with you new credential.';
            } else {
                $_SESSION['create_error'] = 'Something went wrong. Creation failed.';
                storeForm();
            }
        } else {
            $_SESSION['create_error'] = 'Please provide a valid username, email address and password.';
            storeForm();
        }
    } else {
        $result = handlePostFromAdmin();
        $_SESSION['message'] = $result ? 'Success!' : 'Failure.';
    }

    header("Location: admin.php");
    exit;
} else {
    $users = User::getAllUsers();
}

function storeForm()
{
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['password'] = $_POST['password'];
    $_SESSION['discriminator'] = $_POST['discriminator'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Molijun Inn</title>
    <link rel="stylesheet" href="./main.css">
</head>

<body class="box-border bg-gray-100">
    <header class="flex justify-center py-4">
        <nav class="flex justify-between w-4/5 max-w-5xl">
            <a href="./index.php" style="text-decoration: none; color: black;">
                <h1 class="text-2xl font-bold">Molijun Inn</h1>
            </a>
            <ul>
                <li><span class="mx-4"><?= $_SESSION['user_name'] ?></span><a class="btn" href="./logout.php ?>">Sign out</a></li>
            </ul>
        </nav>
    </header>
    <main class="bg-white">
        <?php if (isset($_SESSION['message'])) : ?>
            <script>
                alert('<?= $_SESSION['message'] ?>')
            </script>
        <?php
            unset($_SESSION['message']);
        endif ?>
        <section class="flex justify-center px-4 py-8">
            <div class="w-4/5 max-w-5xl">
                <div class="w-full my-2 border-y-2 border-transparent text-center  hover:border-green-800 hover:cursor-pointer">
                    <span class="font-bold text-lg text-green-800" id="create">Create New User</span>
                    <div>
                        <?php if (!empty($_SESSION['create_success'])) : ?>
                            <small class="text-sm text-green-500"><?= $_SESSION['create_success'] ?></small>
                        <?php unset($_SESSION['create_success']);
                        elseif (!empty($_SESSION['create_error'])) : ?>
                            <small class="text-sm text-red-500"><?= $_SESSION['create_error'] ?></small>
                            <?php unset($_SESSION['create_error']) ?>
                        <?php endif ?>
                    </div>
                    <div>
                        <small class="text-sm text-red-500 hidden" id="match_error">Passwords don't match.</small>
                    </div>
                </div>
                <form id="createForm" class="bg-white w-full" method="POST">
                    <table id="createTable" class="w-full <?= isset($_SESSION['username']) ? '' : 'hidden' ?>">
                        <tbody>
                            <tr class="grid grid-cols-9 gap-2">
                                <th class="col-span-1 text-right"><label for="username">Username:</label></th>
                                <td class="col-span-2">
                                    <input id="username" class="bg-gray-100 w-full border border-green-800 rounded" type="text" id="username" name="username" value="<?php if (isset($_SESSION['username'])) {
                                                                                                                                                                            echo ($_SESSION['username']);
                                                                                                                                                                            unset($_SESSION['username']);
                                                                                                                                                                        } ?>">
                                </td>
                                <th class="col-span-2 text-right"><label for="email">Email:</label></th>
                                <td class="col-span-2">
                                    <input id="email" class="bg-gray-100 w-full border border-green-800 rounded" type="email" id="email" name="email" value="<?php if (isset($_SESSION['email'])) {
                                                                                                                                                                    echo ($_SESSION['email']);
                                                                                                                                                                    unset($_SESSION['email']);
                                                                                                                                                                } ?>">
                                </td>
                                <td class="col-span-2">
                                    <select id="discriminator" class="px-2 py-1 w-full border border-green-800 rounded bg-gray-100" name="discriminator">
                                        <option value="admin" <?= !empty($_SESSION['discriminator']) && $_SESSION['discriminator'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                                        <option value="customer" <?= !empty($_SESSION['discriminator']) && $_SESSION['discriminator'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="grid grid-cols-9 gap-2">

                                <th class="col-span-1 text-right"><label for=" password">Password:</label></th>
                                <td class="col-span-2">
                                    <input class="bg-gray-100 w-full border border-green-800 rounded" type="password" id="password" name="password" value="<?php if (isset($_SESSION['password'])) {
                                                                                                                                                                echo ($_SESSION['password']);
                                                                                                                                                                unset($_SESSION['password']);
                                                                                                                                                            } ?>">
                                </td>
                                <th class="col-span-2 text-right"><label for="password">Confirm Password:</label></th>
                                <td class="col-span-2">
                                    <input class="bg-gray-100 w-full border border-green-800 rounded" type="password" id="password2" name="password2" value="<?php if (isset($_SESSION['password2'])) {
                                                                                                                                                                    echo ($_SESSION['password2']);
                                                                                                                                                                    unset($_SESSION['password2']);
                                                                                                                                                                } ?>">
                                </td>
                                <td class="col-span-2"><button type="submit" class="btn w-full border border-green-800 rounded">submit</button></td>
                            </tr>
                            <input type="hidden" name="insert" value="insert">
                        </tbody>
                    </table>
                </form>
            </div>
        </section>
        <section class="flex justify-center px-4 py-8">
            <div class="w-4/5 max-w-5xl">
                <table class="w-full">
                    <thead class="text-lg">
                        <tr class="grid grid-cols-9 gap-2">
                            <th class="col-span-1"></th>
                            <th class="col-span-1"></th>
                            <th class="col-span-2">username</th>
                            <th class="col-span-3">email</th>
                            <th class="col-span-2">role</th>
                        </tr>
                    </thead>
                </table>
                <?php foreach ($users as $u) : ?>
                    <form method="post">
                        <table class="w-full">
                            <tbody>
                                <input type="hidden" name="user_id" value="<?= $u->user_id ?>">
                                <tr class="border-b-2 border-black grid grid-cols-9  gap-2 text-center py-1 hover:bg-gray-100">
                                    <td class="col-span-1">
                                        <input type="button" class="btn-secondary w-full edit" value="edit">
                                    </td>
                                    <td class="col-span-1">
                                        <input type="submit" class="btn-secondary w-full" name="delete" value="delete">
                                    </td>
                                    <td class="col-span-2"><?= $u->user_name ?></td>
                                    <td class="col-span-3"><?= $u->email ?></td>
                                    <td class="col-span-2"><?= $u->discriminator === 'admin' ? 'Administrator' : 'Customer' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                <?php endforeach ?>
            </div>
        </section>
    </main>
    <script src="./js/admin.js"></script>
</body>

</html>