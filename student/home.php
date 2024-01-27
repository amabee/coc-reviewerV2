<?php
session_start();
include '../server_conn/conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

if (empty($_SESSION['user_id'])) {
    header("Location: ../unauthorized.php");
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
    <title>Student Home</title>


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
            <h1 class="title mx-auto">Quick Options</h1>
            <hr>
            <?php
            if ($user_id != '') {
            ?>
                <div class="row g-0 g-xl-5 g-xxl-7">
                    <div class="col-xl-6">
                        <div class="card w-100">
                            <h5 class="card-header">Comments and Bookmarks</h5>
                            <div class="card-body text-left">
                                <div>
                                    <h6 class="card-title">Bookmarked Lesson: <span> <?= $total_comments; ?> </span></h6>
                                    <button type="button" class="btn btn-success">View Bookmarks</button>
                                </div>
                                <div class="mt-3">
                                    <h6 class="card-title">Total Comments Made: <span> <?= $total_bookmarked; ?></span></h6>
                                    <button type="button" class="btn btn-success">View Comments</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
            }
                ?>
                <div class="col-xl-6">
                    <div class="card w-100">
                        <h5 class="card-header">Categories</h5>
                        <div class="card-body text-left">
                            <ul>
                                <li><button type="button" class="btn bg-dark-subtle mb-2 cstyle"><i class="fa-solid fa-gavel"></i> Criminal Law And Jurisprudence</button></li>
                                <li><button type="button" class="btn bg-dark-subtle mb-2 cstyle">Law Enforcement Administration</button></li>
                                <li><button type="button" class="btn bg-dark-subtle mb-2 cstyle">Forensics/Criminalistics</button></li>
                                <li><button type="button" class="btn bg-dark-subtle mb-2 cstyle">Crime Detection and Investigation</button></li>
                                <li><button type="button" class="btn bg-dark-subtle mb-2 cstyle">Sociology of Crimes and Ethics</button></li>
                                <li><button type="button" class="btn bg-dark-subtle mb-2 cstyle">Correctional Administration</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
                <!-- LATEST LESSONS AREA -->
                <h1 class="title mx-auto">Latest Lessons</h1>
                <hr>
                <div class="container">
                    <div class="row pt-5 m-auto">
                        <?php
                        $select_courses = $conn->prepare("SELECT * FROM `tbl_lessons` WHERE status = ? ORDER BY date DESC LIMIT 3");
                        $select_courses->execute(['active']);
                        if ($select_courses->rowCount() > 0) {
                            while ($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)) {
                                $course_id = $fetch_course['lesson_id'];

                                $select_tutor = $conn->prepare("SELECT * FROM `tbl_teachers` WHERE teacher_id = ?");
                                $select_tutor->execute([$fetch_course['teacher_id']]);
                                $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
                        ?>

                                <div class="col-sm-12 col-md-6 col-lg-4 pb-3">
                                    <div class="card card-custom bg-white border-white border-0">
                                        <div class="card-custom-img" style="background-image: url(http://res.cloudinary.com/d3/image/upload/c_scale,q_auto:good,w_1110/trianglify-v1-cs85g_cc5d2i.jpg);"></div>

                                        <div class="card-custom-avatar">
                                            <img class="img-fluid" src="../tmp/<?= $fetch_tutor['image']; ?>" alt="Avatar" />
                                        </div>
                                        <div class="card-body" style="overflow-y: auto">
                                            <h4 class="card-title">
                                                <?= $fetch_tutor['firstname']; ?>
                                                <?= $fetch_tutor['lastname']; ?>
                                            </h4>

                                            <p class="card-subtitle mt-3 mb-3"><b>LESSON TITLE: </b><?= $fetch_course['lesson_title']; ?></p>
                                            <p class="card-subtitle"><b>LESSON DESCRIPTION: </b><?= $fetch_course['lesson_desc']; ?></p>
                                        </div>
                                        <div class="card-footer" style="background: inherit; border-color: inherit;">
                                            <a href="#" class="btn btn-success">View</a>
                                            <a href="#" class="btn btn-outline-success">Bookmark</a>
                                        </div>
                                    </div>
                                </div>

                        <?php
                            }
                        } else {
                            echo '<div class="card text-center">
                            <div class="card-header">
                                Error
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle">No Lessons Uploaded Yet</h6>
                                <p class="card-text">If the error persists, please contact your teacher to address the issue</p>
                            </div>
                          </div>';
                        }
                        ?>

                    </div>
                </div>
                <!-- LATEST LESSONS AREA -->
        </div>

    </div>

</body>

</html>