<?php 

include 'db_connect.php';

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])){
    
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    if(empty($username_err) && empty($password_err)){
        $prepare = $conn->prepare("SELECT UserID, Username, Password FROM User_List WHERE Username=?");
        $prepare->bind_param("s", $username);
        if($prepare->execute()){
            $prepare->store_result();
            if($prepare->num_rows() == 1){
                $prepare->bind_result($id, $username, $hashed_password);
                if($prepare->fetch()){
                    if(password_verify($password, $hashed_password)){
                        session_start();
                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username; 

                        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                            header("location: actionOptions.php");
                            exit;
                        }

                    } else {  $login_err = "Invalid username or password."; }
                }
            } else { $login_err = "Invalid username or password."; }
        } else {  echo "Oops! Something went wrong. Please try again later.";  }
        $prepare->close();
    }
    $conn->close();
} 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login Page</title>
    <style>
        body {
            font-family: 'Trebuchet MS', sans-serif;
            background-color: rgb(249, 225, 196);
            font-size: 80%;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .pageForming {
            border: 6px solid #394b33;
            padding: 30px;
            border-radius: 15px;
            background-color: rgb(187, 168, 147);
            width: 80%;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .Login {
            position: relative;
            margin: 0 auto;
            width: 310px;
        }
        .Login:before {
            content: '';
            position: absolute;
            top: -8px;
            right: -8px;
            bottom: -8px;
            left: -8px;
            z-index: -1;
            background: #47ae5581;
            border-radius: 10px;
        }
        .Login h1 {
            border: 3px solid green;
            border-radius: 10px 30px;
            margin-bottom: 20px;
            background-color: rgb(131, 152, 103);
            text-align: center;
            padding: 10px;
        }
        .Login p.submit {
            text-align: right;
        }
        
        .Register-help {
            margin: 20px;
            font-size: 11px;
            color: rgb(245, 103, 103);
            text-align: center;
            text-shadow: 0 1px #c4f1bc;
        }
        
        .Register-help a {
            color: #078bea;
            text-decoration: none;
        }
        
        .Register-help a:hover {
            text-decoration: underline;
        }
        input{
            font-family: 'Lucida Grande', Tahoma, Verdana, sans-serif;
            font-size: 14px;
        }
        input[type=text], input[type=password] {
            margin: 5px;
            padding: 0 10px;
            width: 200px;
            height: 34px;
            color: #404040;
            background: white;
            border: 1px solid;
            border-color: #c4c4c4 #d1d1d1 #d4d4d4;
            border-radius: 2px;
            outline: 5px solid #eff4f7;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.12);
        }
        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 5px;
        }
        input[type="password"],
        input[type="text"],
        input[type="number"],
        select {
            width: 90%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type=submit] {
            padding: 0 18px;
            height: 29px;
            font-size: 12px;
            font-weight: bold;
            color: #527881;
            text-shadow: 0 1px #e3f1f1;
            background: #cde5ef;
            border: 1px solid;
            border-color: #b4ccce #b3c0c8 #9eb9c2;
            border-radius: 16px;
        }
        input[type=submit]:active {
            background: #cde5ef;
            border-color: #9eb9c2 #b3c0c8 #b4ccce;
            -webkit-box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.2);
            box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <section class="pageForming">
        <div class="Login">
            <h1> Login Page </h1>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <p>
                    <label style="font-size: 18px;">Username / Email</label>
                    <input type="text" name="username" placeholder="Username or Email">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </p>
                <p>
                    <label style="font-size: 18px;">Password</label>
                    <input type="password" name="password" value="" placeholder="Password">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </p>
                <p class="remember_me">
                    <label>
                        <input type="checkbox" name="remember_me" value="" id="remember_meo">
                        Remember Me On This Device
                    </label>
                </p>
                <p class="submit">
                    <input type="submit" name="submit" value="Login">
                </p>
                <span class="invalid-feedback"><?php echo $login_err; ?></span>
            </form>
        </div>
        <div class="Register-help">
            <p>Forgot Your Password? <a href="#">Click Here</a></p>
        </div>
    </section>
</body>
</html>
