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
    <title>Document</title>
    <link rel="stylesheet" href="https://unpkg.com/mvp.css">
</head>

<body>
    <header>
        <h1>Molijun Inn</h1>
    </header>
    <main>
        <section>
            <form action="login.php" method="POST">
                <header>
                    <h2>Login</h2>
                </header>
                <?php if (!empty($_SESSION['login_error'])) : ?>
                    <small style="color: red;"><?= $_SESSION['login_error'] ?></small>
                    <?php unset($_SESSION['login_error']) ?>
                <?php endif ?>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php if (isset($_SESSION['email'])) {
                                                                        echo ($_SESSION['email']);
                                                                        unset($_SESSION['email']);
                                                                    } ?>">
                <label for=" password">Password</label>
                <input type="password" id="password" name="password" value="<?php if (isset($_SESSION['password'])) {
                                                                                echo ($_SESSION['password']);
                                                                                unset($_SESSION['password']);
                                                                            } ?>">
                <button type="submit">Submit</button>
            </form>
        </section>
    </main>
</body>

</html>