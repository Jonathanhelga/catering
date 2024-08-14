<?php
include 'db_connect.php';


$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])){
    if(empty(trim($_POST["username"]))){
        $username_err = "this field can't be empty";
    }
    else if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    }
    else{
        if($stmt = $conn->prepare("SELECT UserID FROM User_List WHERE Username=?")){
            $param_username = trim($_POST["username"]);
            $stmt->bind_param("s",$param_username);
            if($stmt->execute()){
                $result = $stmt->get_result();
                if($result->num_rows > 0){ 
                    $username_err = "Username or email already exist, Please create new Username or input the correct password" . "<br>"; 
                }
                else{ 
                    $username = trim($_POST["username"]); 
                    if(empty(trim($_POST["password"]))){ $password_err = "Please enter a password.";} 
                    else if(strlen(trim($_POST["password"])) < 6){ $password_err = "Password must have atleast 6 characters.";} 
                    else{ $password = trim($_POST["password"]); }   

                    if(empty(trim($_POST["confirm_password"]))){ $confirm_password_err = "Please confirm password.";      } 
                    else{
                        $confirm_password = trim($_POST["confirm_password"]);
                        if(empty($password_err) && ($password != $confirm_password)){ $confirm_password_err = "Password did not match.";}
                    }
                }
            }
            else{ echo "OOpss! Something went not right, Sorry Try again later.."; }
            $stmt->close();
        }
    }
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        $prepare = $conn->prepare("INSERT INTO User_List (Username, Password) VALUES (?, ?)");
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_BCRYPT);
        $prepare->bind_param("ss", $param_username, $param_password);
        if($prepare->execute()){ header("location: loginPage.php");}
        else{ echo "somthing went wrong while executing.."; }
        $prepare->close();
    }
    unset($_POST);
    unset($_SESSION);
    $conn->close();
    
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Register Page</title>
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
            .Register {
                position: relative;
                margin: 0 auto;
                width: 310px;
            }
            .Register:before {
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
            .Register h1 {
                border: 3px solid green;
                border-radius: 10px 30px;
                margin-bottom: 20px;
                background-color: rgb(131, 152, 103);
                text-align: center;
                padding: 10px;
            }
            .Register p.submit {
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
            <div class="Register">
                <h1> Register Page </h1>
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
                    <p>
                        <label style="font-size: 18px;">Confirm Password</label>
                        <input type="password" name="confirm_password" value="" placeholder="Confirm Password">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </p>
                    <p class="remember_me">
                        <label>
                            <input type="checkbox" name="remember_me" value="" id="remember_meo">
                            Remember Me On This Device
                        </label>
                    </p>
                    <p class="submit">
                        <input type="submit" name="submit" value="Register">
                    </p>
                </form>
            </div>
            <div class="Register-help">
                <p>Already Have an Account? <a href="loginPage.php">Click Here</a></p>
            </div>
            <div class="Register-help">
                <p>Forgot Your Password? <a href="#">Click Here</a></p>
            </div>

        </section>

    </body>
</html>