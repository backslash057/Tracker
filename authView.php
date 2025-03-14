<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login - Tracker</title>
	<link rel="stylesheet" type="text/css" href="static/css/auth.css">
</head>
<body>
    <div class="container">
        <div class="error_frame"></div>
        <form class="form" method="POST" action="">
            <div class="form-entry">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-entry">
                <label for="pwd">Password</label>
                <input type="password" name="pwd" id="pwd" required>
            </div>
            <div class="remember-check">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me</label>
            </div>
            <div class="submit-box">
                <input class="submit" type="submit" id="submit" value="Sign Up" />
                <div class="alternative">Already an account? <a href="login.php">Log In</a></div>
            </div>
        </form>
    </div>

	<script type="text/javascript" src="static/js/auth.js"></script>
</body>
</html>
