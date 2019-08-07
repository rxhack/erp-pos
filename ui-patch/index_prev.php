<?php
    session_start();
    if(isset($_SESSION['user'])) {
        header('location: ./user.php');
    }

    include_once '../models/User.php';

    $user = new User();

    if (isset($_POST['signup'])) {
        $r = $user->registration($_POST);
    }

    if (isset($_POST['signin'])) {
        $r = $user->login($_POST);
        if ($r['pass']) {
            $_SESSION['user']=$_POST['pnumber'];
            header('location: ./user.php');
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>login</title>

</head>
<body>
    <header>
        <h1>LOGO</h1>
        <div class="nav">
            <a href="#">CONTACT</a>
            <a href="#">ABOUT US</a>
            <a href="index.php">LOGIN</a>
        </div>
    </header>
    <div class="container">
        <form method="post" class="registration">
            <h1>SIGN-UP</h1>
            <div>
                <?php
                    if (isset($r) && isset($_POST['signup'])) {
                        if (is_array($r['msg'])) {
                            foreach ($r['msg'] as $msg) {
                                echo '<p style="color:orange;font-family:Calibri;">'.$msg.'</p>';
                            }
                        } else {
                            echo '<p style="color:orange;font-family:Calibri;">'.$r['msg'].'</p>';
                        }
                    }
                ?>
            </div>
        <input type="text"placeholder="enter name" name="username" class="ibox" required><br>
        <input type="tel" placeholder="enter phone number" name="pnumber" class="ibox"required><br>
        <input type="number" placeholder="enter unique id" name="uid" class="ibox"required><br>
        <input type="number" placeholder="confirm unique id" name="cuid" class="ibox"required><br>
        <input type="submit"value="sign up" name="signup" class="btn">
    </form>
        <form method="post"class="login">
            <h1>SIGN-IN</h1>
            <div>
                <?php
                    if (isset($r) && isset($_POST['signin'])) {
                        if (is_array($r['msg'])) {
                            foreach ($r['msg'] as $msg) {
                                echo '<p style="color:orange;font-family:Calibri;">'.$msg.'</p>';
                            }
                        } else {
                            echo '<p style="color:orange;font-family:Calibri;">'.$r['msg'].'</p>';
                        }
                    }
                ?>
            </div>
        <input type="tel" placeholder="enter phone number" name="pnumber" class="ibox"required><br>
        <input type="number" placeholder="enter unique id" name="uid" class="ibox"required><br>
        <input type="submit"value="sign in" name="signin" class="btn">
    </form>
    </div>

</body>
</html>
