<?php

session_start();


if (
    $_POST &&
    isset($_POST['email']) &&
    isset($_POST['password'])
) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (!($email = filter_var($email, FILTER_VALIDATE_EMAIL)) || !$_POST['password']) {
        storeForm();
        $_SESSION['login_error'] = 'Please provide a valid email address and password.';
        header('Location: login.php');
        exit;
    }

    require('./connect.php');
    require('./models/User.php');
    $query = 'SELECT * FROM users WHERE email = :email';
    try {
        $statement = $db->prepare($query);
        $statement->execute(['email' => $email]);
        $statement->setFetchMode(PDO::FETCH_CLASS, 'User');
        $user = $statement->fetch();
    } catch (PDOException $e) {
        exit($e->getMessage());
    }

    $authPassed = $user && password_verify($_POST['password'], $user->password);
    if ($authPassed) {
        $_SESSION['user'] = $user;
        setcookie('');
        header('Location: index.php');
        exit;
    } else {
        storeForm();
        $_SESSION['login_error'] = 'Your user credential is invalid.';
        header('Location: login.php');
        exit;
    }
}

function storeForm()
{
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

<body class="box-border">
    <header class="flex justify-center py-4">
        <nav class="flex justify-between w-4/5 max-w-5xl">
            <a href="./index.php" style="text-decoration: none; color: black;">
                <h1 class="text-2xl font-bold">Molijun Inn</h1>
            </a>
        </nav>
    </header>
    <main>
        <section class="flex flex-col items-center px-4 py-8">
            <div class="max-w-md w-72">
                <form class="bg-gray-100 p-8 w-full rounded shadow-sm hover:shadow-md" action=" login.php" method="POST">
                    <header class="mb-4 p-8">
                        <h2 class="text-xl font-bold">Login</h2>
                    </header>
                    <?php if (!empty($_SESSION['login_error'])) : ?>
                        <small class="text-sm text-red-500"><?= $_SESSION['login_error'] ?></small>
                        <?php unset($_SESSION['login_error']) ?>
                    <?php endif ?>
                    <label class="block mb-2" for="email">Email</label>
                    <input class="block mb-2 w-full" type="email" id="email" name="email" value="<?php if (isset($_SESSION['email'])) {
                                                                                                        echo ($_SESSION['email']);
                                                                                                        unset($_SESSION['email']);
                                                                                                    } ?>">
                    <label class="block mb-2" for=" password">Password</label>
                    <input class="block mb-8 w-full" type="password" id="password" name="password" value="<?php if (isset($_SESSION['password'])) {
                                                                                                                echo ($_SESSION['password']);
                                                                                                                unset($_SESSION['password']);
                                                                                                            } ?>">
                    <button class="btn" type="submit">Submit</button>
                </form>
            </div>
        </section>
    </main>
</body>

</html>