<?php

	require_once __DIR__ . '/authenticator.php';

	$auth = new Authenticator('127.0.0.1', '', '', '');
	if ($auth->isAuthenticated()) $user = $auth->session('user');

	if (isset($_POST['login'])) {
		if (!$auth->isAuthenticated()) {
		    $email = $auth->sanitize($_POST['email'], FILTER_SANITIZE_EMAIL); $password = $auth->sanitize($_POST['password']);
            try {
                if ($auth->login($email, $password))
                {
                    $user = $auth->session('user');
                }
			} catch (Exception $e) {
			    $error = $e->getMessage();
			}
		}
    }

	if (isset($_POST['register'])) {
	    if (!$auth->isAuthenticated()) {
            $email = $auth->sanitize($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $auth->sanitize($_POST['password']); $confirm = $auth->sanitize($_POST['password_confirm']);
            try {
                if ($auth->register($email, $password, $confirm)) {
                    if ($auth->login($email, $password))
                    {
                        $user = $auth->session('user');
                    }
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
	    }
    }

	if (isset($_POST['disconnect'])) {
	    if ($auth->isAuthenticated()) {
	        if ($auth->logout()) {
	            header("redirect: example.php");
            }
        }
    }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Authenticator | Example</title>
  </head>
  <body>

    <?php if (isset($error) && !empty($error)): ?>
	    <span style="color: red;"><?= $error ?></span>
    <?php endif; ?>

    <?php if (!$auth->isAuthenticated()): ?>
        <h2>Login</h2>
        <form action="example.php" method="POST">
          <div>
            <label for="email">Email</label>
            <input type="text" name="email">
          </div>
          <div>
            <label for="password">Password</label>
            <input type="password" name="password">
          </div>
          <div>
            <button type="submit" name="login">Login</button>
          </div>
        </form>

        <h2>Register</h2>
        <form action="example.php" method="POST">
            <div>
                <label for="email">Email</label>
                <input type="text" name="email">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password">
            </div>
            <div>
                <label for="password_confirm">Confirm password</label>
                <input type="password" name="password_confirm">
            </div>
            <div>
                <button type="submit" name="register">Register</button>
            </div>
        </form>

    <?php endif; ?>

    <?php if ($auth->isAuthenticated()): ?>
        <h1><?= $user->email; ?></h1>
        <a class="btn btn-sm btn-info" href="#logout" onclick="event.preventDefault(); document.getElementById('logout').submit();">
            Logout
            <form id="logout" action="example.php" method="POST">
                <input type="hidden" name="disconnect" value="<?= $user->id; ?>">
            </form>
        </a>
    <?php endif; ?>

  </body>
</html>
