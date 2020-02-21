<?php

/**
 * Class Authenticator.
 *
 * @author Fouyon joshua <fouyon.joshua@gmail.com>
 *
 * @version 1.0.0
 */
class authenticator
{
    /**
     * @var object|array
     */
    private $database;

    /**
     * @var bool
     */
    private $authenticated = false;

    /**
     * Authentication constructor.
     *
     * @param string $hostname
     * @param string $name
     * @param string $username
     * @param string $password
     * @param int    $port
     *
     * @throws Exception
     */
    public function __construct(string $hostname, string $name, string $username, string $password, int $port = 3306)
    {
        try {
            if (session_status() != PHP_SESSION_ACTIVE) {
                session_start();
            }
            $this->database = new PDO("mysql:host={$hostname};port={$port};dbname={$name}", $username, $password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (isset($_SESSION['user'])) {
                $this->authenticated = true;
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * This function is used to login an user.
     *
     * @param string $email
     * @param string $password
     *
     * @throws Exception
     *
     * @return bool
     */
    public function login(string $email, string $password): bool
    {
        if (!$this->validate($email, FILTER_VALIDATE_REGEXP, ['regexp' => '/[\s\S]/'])) {
            throw new Exception('Please fill an email address..');
        }
        if (!$this->validate($password, FILTER_VALIDATE_REGEXP, ['regexp' => '/[\s\S]/'])) {
            throw new Exception('Please fill your password..');
        }
        if (!$this->validate($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('The format of your email address does not seem correct..');
        }
        if (!$this->validate($password, FILTER_VALIDATE_REGEXP, ['regexp' => '/^.{8,}$/'])) {
            throw new Exception('Your password must contain at least 8 characters');
        }

        try {
            $state = $this->database->prepare('SELECT * FROM users WHERE email = :email');
            $state->bindParam(':email', $email, PDO::PARAM_STR);

            if ($state->execute()) {
                $user = $state->fetch(PDO::FETCH_OBJ);
                $state->closeCursor();
            }

            if (empty($user)) {
                throw new Exception('No users found with this email..');
            }
            if (password_verify(sha1($password), $user->password)) {
                $this->authenticated = true;
                $this->session('user', $user);
            } else {
                throw new Exception("Password doesn't not match !");
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        return true;
    }

    /**
     * This function is used to register a new user.
     *
     * @param string $email
     * @param string $password
     * @param string $confirm
     *
     * @throws Exception
     *
     * @return bool
     */
    public function register(string $email, string $password, string $confirm): bool
    {
        if (!$this->validate($email, FILTER_VALIDATE_REGEXP, ['regexp' => '/[\s\S]/'])) {
            throw new Exception('Please fill an email address..');
        }
        if (!$this->validate($password, FILTER_VALIDATE_REGEXP, ['regexp' => '/[\s\S]/'])) {
            throw new Exception('Please fill a password..');
        }
        if (!$this->validate($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('The format of your email address does not seem correct..');
        }
        if (!$this->validate($password, FILTER_VALIDATE_REGEXP, ['regexp' => '/^.{8,}$/'])) {
            throw new Exception('Your password must contain at least 8 characters');
        }
        if ($password != $confirm) {
            throw new Exception("Passwords don't match..");
        }
        $hash = password_hash(sha1($password), PASSWORD_BCRYPT);

        try {
            $state = $this->database->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
            $state->bindParam(':email', $email, PDO::PARAM_STR);
            $state->execute();

            $count = $state->fetchColumn();
            if ($count <= 0) {
                $state = $this->database->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
                $state->bindParam(':email', $email, PDO::PARAM_STR);
                $state->bindParam(':password', $hash, PDO::PARAM_STR);

                if ($state->execute()) {
                    $state->closeCursor();
                }
            } else {
                throw new Exception('An account with this email already exist..');
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        return true;
    }

    /**
     * This function is used to logout an user.
     *
     * @return bool
     */
    public function logout(): bool
    {
        if ($this->authenticated) {
            $this->session('user', []);
            session_destroy();
            $this->authenticated = false;

            return true;
        }

        return false;
    }

    /**
     * This function is used to determine if an user is logged.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    /**
     * This function is used to return the authenticated user.
     *
     * @return null|object
     */
    public function user(): object
    {
        return (object) $this->session('user');
    }

    /**
     * This function is used to validate any value.
     *
     * @param $value
     * @param int   $filter
     * @param array $options
     *
     * @return bool
     */
    public function validate($value, int $filter, array $options = []): bool
    {
        if (filter_var($value, $filter, ['options' => $options])) {
            return true;
        }

        return false;
    }

    /**
     * This function is used to sanitize any value.
     *
     * @param string   $value
     * @param null|int $filter
     * @param array    $options
     *
     * @return mixed
     */
    public function sanitize(string $value, int $filter = null, array $options = [])
    {
        if ($filter == null) {
            return trim($value);
        }

        return filter_var(trim($value), $filter, ['options' => $options]);
    }

    /**
     * This function is used to read/write from session.
     *
     * @param string                   $key
     * @param null|string|array|object $value
     *
     * @return string|array|object
     */
    public function session(string $key, $value = null)
    {
        if ($value === null) {
            return $_SESSION[$key];
        }

        $_SESSION[$key] = $value;
    }
}
