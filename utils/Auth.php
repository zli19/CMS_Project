<?php
require_once('./models/User.php');
require_once('./models/Token.php');


class Auth
{
    function authenticateUser(string $email, string $password)
    {
        $user = User::queryUserByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }

    function isLoggedIn()
    {
        // check session first
        if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_name']) && !empty($_SESSION['discriminator'])) return true;
        // check cookies
        if (!empty($_COOKIE['user_token'])) {

            // fetch the token object from db
            $token = Token::queryTokenByTokenNo($_COOKIE['user_token']);

            // check if token is valid
            if (
                $token
                && !$token->is_expired
            ) {
                if ($token->expiry_date >= date("Y-m-d H:i:s", time())) {
                    // fetch user information using the token
                    $user = User::queryUserById($token->user_id);

                    if ($user) {
                        // set $_SESSION to store logged-in state
                        $this->setLoginSession($user);
                        return true;
                    }
                } else {
                    $token->markAsExpired();
                    setcookie('user_token', '', time() - 3600);
                }
            }
        }
        return false;
    }

    // set $_SESSION to store user's logged-in state
    function setLoginSession(User $user)
    {
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['user_name'] = $user->user_name;
        $_SESSION['discriminator'] = $user->discriminator;
    }

    function setCookieAndToken(User $user)
    {
        $cookieExpiryTime = time() + (24 * 60 * 60); // one day
        $token_no = md5(uniqid() . rand(1000000, 9999999));
        setcookie('user_token', $token_no, $cookieExpiryTime);

        $token = new Token();
        $tokenExpiryTime = time() + (2 * 60 * 60); // 2 hours
        $token->user_id = $user->user_id;
        $token->token_no = $token_no;
        $token->expiry_date = date("Y-m-d H:i:s", $tokenExpiryTime);

        $token->insertToken();
    }

    function logout()
    {
        if (!empty($_COOKIE['user_token'])) {
            $token = Token::queryTokenByTokenNo($_COOKIE['user_token']);
            if ($token) $token->markAsExpired();
            setcookie('user_token', '', time() - 3600);
        }
        session_destroy();
    }
}
