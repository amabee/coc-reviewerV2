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

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons</title>

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
            <h1 class="title mx-auto">All Lessons</h1>
            <hr>
            <div class="row g-0 g-xl-5 g-xxl-8">
                <div class="col-xl-12">
                    <div class="container">
                        <div class="row pt-5 m-auto">
                            <?php
                            $select_courses = $conn->prepare("SELECT tbl_classlessons.lesson_id, tbl_studentclasses.section_name, tbl_studentclasses.student_id, tbl_lessons.status, 
            tbl_lessons.teacher_id, tbl_lessons.date, tbl_lessons.lesson_desc, tbl_lessons.lesson_title, tbl_lessons.thumb
            FROM tbl_studentclasses
            INNER JOIN tbl_classlessons ON tbl_classlessons.section_name = tbl_studentclasses.section_name
            INNER JOIN tbl_lessons ON tbl_classlessons.lesson_id = tbl_lessons.lesson_id 
            WHERE tbl_lessons.status = ? AND tbl_studentclasses.student_id = ? ORDER BY date DESC;
            ");
                            $select_courses->execute(['active', $user_id]);
                            if ($select_courses->rowCount() > 0) {
                                while ($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)) {
                                    $course_id = $fetch_course['lesson_id'];
                                    $select_tutor = $conn->prepare("
                                        SELECT * FROM tbl_teachers
                                        INNER JOIN tbl_section ON tbl_teachers.teacher_id = tbl_section.teacher_id
                                        WHERE tbl_teachers.teacher_id = ?;
                                    ");

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
                                                <a href="materials.php?get_id=<?= $course_id; ?>" class="btn btn-success" onclick="setTempCookie('get_materialsPage_id', <?= $course_id ?>)">View</a>
                                                <script>
                                                    function setTempCookie(cookieName, cookieValue) {
                                                        document.cookie = cookieName + '=' + cookieValue + '; path=/';
                                                    }
                                                </script>
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
                </div>
            </div>
        </div>
    </div>
</body>

</html>