<?php
session_start();
include '../server_conn/conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

if (empty($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$select_comments = $conn->prepare("SELECT * FROM `tbl_comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

$select_bookmark = $conn->prepare("SELECT * FROM `tbl_bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>

    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="style/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style/custom_style/custom_switch.css">
    <link rel="stylesheet" href="style/custom_style/custom_card.css">
    <link rel="stylesheet" href="style/custom_style/home_cstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,500&amp;subset=latin-ext" rel="stylesheet">
</head>

<body>
    <?php
    include("misc/navbar.php");
    ?>
    <div class="wrapper">
        <?php
        include("misc/sidebar.php");
        ?>
        <div class="main p-3">
            <h1 class="title mx-auto">Student Profile</h1>
            <hr>
            <div class="row g-0 g-xl-5 g-xxl-10">
                <div class="col-xl-12">
                    <div class="card mt-4">
                        <div class="card-header">
                            <div class="card-body text-center">
                                <div>
                                    <img src="../tmp/<?= $fetch_profile['image']; ?>" alt="profile" class="img img-fluid mb-3 rounded-circle" style="width: 100px; height: 100px;">

                                    <h4 class="card-title text-center fw-bold"><?= $fetch_profile['firstname']; ?> <?= $fetch_profile['lastname']; ?></h4>
                                    <h6 class="text-center card-subtitle">Student ID</h6>
                                </div>

                                <div class="self-center mt-3">
                                    <button type="button" class="btn btn-success">Update Profile</button>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-xl-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="card-body text-left">
                                                    <div class="bookmark-box">
                                                        <i class="bi bi-bookmark-fill"></i> Bookmarked Lessons: 0
                                                    </div>
                                                    <div class="self-center mt-3">
                                                        <button type="button" class="btn btn-success">View Bookmarks</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="card-body text-left">
                                                    <div class="bookmark-box">
                                                        <i class="bi bi-chat-heart-fill"></i> Comments Made: 0
                                                    </div>
                                                    <div class="self-center mt-3">
                                                        <button type="button" class="btn btn-success">View Comments</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>