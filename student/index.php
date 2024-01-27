<?php
include '../server_conn/conn.php';
session_start();

if (isset($_COOKIE['user_id']) || isset($_SESSION['user_id'])) {
    $user_id = $_COOKIE['user_id'];
    header('Location: home.php');
    exit;
}

if (isset($_POST['submit'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $password = $_POST['pass'];

    $select_user = $conn->prepare("SELECT id, password FROM `tbl_students` WHERE email = ? AND isActive = 'active' LIMIT 1");
    $select_user->execute([$email]);

    $hash = hash('sha256', $password);

    if ($select_user->rowCount() > 0) {
        $row = $select_user->fetch(PDO::FETCH_ASSOC);
        
        if ($hash === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['student'];
            header('Location: home.php');
            exit;
        } else {
            $error_message = 'Incorrect email or password!';
            echo "<script>document.addEventListener('DOMContentLoaded', function() {
                $('#invalidCredentialsModal').modal('show');
            });</script>";
        }
    } else {
        $error_message = 'User not found!';
        echo "<script>document.addEventListener('DOMContentLoaded', function() {
            $('#invalidCredentialsModal').modal('show');
        });</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/custom_style/cstyle.css">
    <title>Login</title>
</head>

<body>
    <div class="wrapper">

        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger text-center">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div id="invalidCredentialsModal" class="modal fade">
            <div class="modal-dialog modal-confirm">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                        <img src="assets/undraw_warning_re_eoyh.svg" class="img-thumbnail" style="background-color: transparent; border:0;" alt="">
                    </div>
                    <div class="modal-body text-center">
                        <h4>Invalid Credentials</h4>
                        <p>
                            <?php echo $error_message; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container main">

            <div class="row">
                <div class="col-md-6 side-image">
                    <img src="assets/logo.png" alt="coc_logo">
                </div>
                <div class="col-md-6 right">

                    <div class="input-box">
                        <form method="post" action="">
                            <header>User Authentication</header>
                            <div class="input-field">
                                <input type="text" class="input" id="email" name="email" required autocomplete="off">
                                <label for="email">Email</label>
                            </div>
                            <div class="input-field">
                                <input type="password" class="input" id="pass" name="pass" required>
                                <label for="pass">Password</label>
                            </div>
                            <div class="input-field">
                                <input type="submit" class="submit" name="submit" value="Sign In">
                            </div>
                        </form>
                        <div class="signin">
                            <span>Don't have an account yet? <a href="#">Contact the Faculty Office</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div id="accountProblem" class="modal fade">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <img src="assets/undraw_contact_us_re_4qqt.svg" class="img-thumbnail" style="background-color: transparent; border:0;" alt="">
                </div>
                <div class="modal-body text-center">
                    <h4>Please contact the Admin or Faculty!</h4>
                    <p>Account creation is handled by the faculty or the admin.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>