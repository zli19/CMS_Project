<?php
require_once('./models/User.php');
require_once('./models/Token.php');


class Auth
{
    function authenticateUser(string $email, string $password)
    {

        try {
            $user = User::queryUserByEmail($email);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }

        if ($user && password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }

    function isLoggedIn()
    {

        // check session first
        if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) return true;
        // check cookies
        if (!empty($_COOKIE['user_token'])) {

            // fetch the token object from db
            try {
                $token = Token::queryTokenByTokenNO($_COOKIE['user_token']);
            } catch (PDOException $e) {
                exit($e->getMessage());
            }

            // check if token is valid
            if (
                $token
                && !$token->is_expired
            ) {
                if ($token->expiry_date >= date("Y-m-d H:i:s", time())) {
                    // fetch user information using the token
                    try {
                        $user = User::queryUserById($token->user_id);
                    } catch (PDOException $e) {
                        exit($e->getMessage());
                    }

                    if ($user) {
                        // set $_SESSION to store logged-in state
                        $this->setLoginState($user);
                        return true;
                    }
                } else {
                    $this->clearCookieAndToken($token->token_id);
                }
            }
        }

        return false;
    }

    // set $_SESSION to store user's logged-in state
    function setLoginState(User $user)
    {
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['role'] = $user->discriminator;
    }

    function setCookieAndToken(User $user)
    {
        $cookieExpiryTime = time() + (2 * 60 * 60); // 2 hours
        $token_no = md5(uniqid() . rand(1000000, 9999999));
        setcookie('user_token', $token_no, $cookieExpiryTime);

        $token = new Token();
        $tokenExpiryTime = time() + 60; // 1 min
        $token->user_id = $user->user_id;
        $token->token_no = $token_no;
        $token->expiry_date = date("Y-m-d H:i:s", $tokenExpiryTime);
        try {
            Token::insert($token);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    function clearCookieAndToken(int $token_id)
    {
        setcookie('user_token', '', time() - 3600);
        try {
            Token::markAsExpiredById($token_id);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}
