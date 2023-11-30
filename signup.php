<?php
session_start();

require('./utils/Auth.php');
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
if ($isLoggedIn) {
    header('Location: index.php');
}

if (
    $_POST &&
    isset($_POST['username']) &&
    isset($_POST['email']) &&
    isset($_POST['password'])
) {
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && $_POST['username'] &&  $_POST['password']) {

        require('./processPost.php');

        $result = handlePostFromAdmin();

        if ($result) {
            $_SESSION['message'] = 'Success! You can login with you new credential.';
            header('Location: login.php');
            exit;
        } else {
            $_SESSION['signup_error'] = 'Something went wrong when creating your credential.';
        }
    } else {
        $_SESSION['signup_error'] = 'Please provide a valid username, email address and password.';
    }

    storeForm();
    header("Location: signup.php");
    exit;
}

function storeForm()
{
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['password'] = $_POST['password'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Molijun Inn</title>
    <link rel="stylesheet" href="main.css">
</head>

<body class="box-border bg-gray-100">
    <header class="flex justify-center py-4">
        <nav class="flex justify-between w-4/5 max-w-5xl">
            <a href="./index.php" style="text-decoration: none; color: black;">
                <h1 class="text-2xl font-bold">Molijun Inn</h1>
            </a>
        </nav>
    </header>
    <main>
        <section class="flex flex-col items-center px-4 py-8">
            <div class="max-w-md w-80">
                <form class="bg-white p-8 w-full rounded shadow-sm hover:shadow-md" action="signup.php" method="POST">
                    <header>
                        <h2 class="text-xl font-bold text-green-700 py-4">Create your account</h2>
                        <p class="font-bold py-4">Have an account? <a class="text-blue-700" href="login.php">Log in now</a></p>
                    </header>
                    <?php if (!empty($_SESSION['signup_error'])) : ?>
                        <small class="text-sm text-red-500"><?= $_SESSION['signup_error'] ?></small>
                        <?php unset($_SESSION['signup_error']) ?>
                    <?php endif ?>
                    <label class="block mb-2" for="username">Username</label>
                    <input class="bg-gray-100 block mb-2 w-full" type="text" id="username" name="username" value="<?php if (isset($_SESSION['username'])) {
                                                                                                                        echo ($_SESSION['username']);
                                                                                                                        unset($_SESSION['username']);
                                                                                                                    } ?>">

                    <label class="block mb-2" for="email">Email</label>
                    <input class="bg-gray-100 block mb-2 w-full" type="email" id="email" name="email" value="<?php if (isset($_SESSION['email'])) {
                                                                                                                    echo ($_SESSION['email']);
                                                                                                                    unset($_SESSION['email']);
                                                                                                                } ?>">
                    <label class="block mb-2" for=" password">Password</label>
                    <input class="bg-gray-100 block mb-2 w-full" type="password" id="password" name="password" value="<?php if (isset($_SESSION['password'])) {
                                                                                                                            echo ($_SESSION['password']);
                                                                                                                            unset($_SESSION['password']);
                                                                                                                        } ?>">
                    <label class="block mb-2" for=" password">Confirm Password</label>
                    <small class="text-sm text-red-500 hidden" id="match_error">Passwords don't match.</small>
                    <input class="bg-gray-100 block mb-8 w-full" type="password" id="password2" name="password2" value="<?php if (isset($_SESSION['password2'])) {
                                                                                                                            echo ($_SESSION['password2']);
                                                                                                                            unset($_SESSION['password2']);
                                                                                                                        } ?>">
                    <input type="hidden" name="insert" value="insert">
                    <button type="submit" class="btn">submit</button>
                </form>
            </div>
        </section>
    </main>
    <script src="./js/signup.js"></script>
</body>

</html>