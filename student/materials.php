<?php
session_start();
include '../server_conn/conn.php';

if (empty($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

if (!isset($_COOKIE['get_materialsPage_id'])) {
    header('location:home.php');
    exit();
}

$get_id = $_COOKIE['get_materialsPage_id'];


if (isset($_POST['save_list'])) {

    if ($user_id != '') {

        $list_id = $_POST['list_id'];
        $list_id = filter_var($list_id, FILTER_SANITIZE_STRING);

        $select_list = $conn->prepare("SELECT * FROM `tbl_bookmark` WHERE user_id = ? AND lesson_id = ?");
        $select_list->execute([$user_id, $list_id]);

        if ($select_list->rowCount() > 0) {
            $remove_bookmark = $conn->prepare("DELETE FROM `tbl_bookmark` WHERE user_id = ? AND lesson_id = ?");
            $remove_bookmark->execute([$user_id, $list_id]);
            $message[] = 'Lesson removed!';
        } else {
            $insert_bookmark = $conn->prepare("INSERT INTO `tbl_bookmark`(user_id, lesson_id) VALUES(?,?)");
            $insert_bookmark->execute([$user_id, $list_id]);
            $message[] = 'Lesson saved!';
        }
    } else {
        $message[] = 'please login first!';
    }
}


$pretest_query = $conn->prepare("SELECT * FROM tbl_quiz WHERE lesson_id = ? AND quiz_type = 'pre-test' AND status = 'active'");
$pretest_query->execute([$get_id]);
$pretest_exists = $pretest_query->rowCount() > 0;

$posttest_query = $conn->prepare("SELECT * FROM tbl_quiz WHERE lesson_id = ? AND quiz_type = 'post-test' AND status = 'active'");
$posttest_query->execute([$get_id]);
$posttest_exists = $posttest_query->rowCount() > 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materials</title>

    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="style/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style/custom_style/custom_switch.css">
    <link rel="stylesheet" href="style/custom_style/custom_card.css">
    <link rel="stylesheet" href="style/custom_style/home_cstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,500&amp;subset=latin-ext" rel="stylesheet">

    <style>
        .lesson-card {
            border: 1px solid #dcdcdc;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            position: relative;
        }

        .lesson-card .materials-badge {
            position: absolute;
            top: 35px;
            left: 35px;
            width: 120px;
            height: 40px;
            background-color: rgba(0, 0, 0, .3);
            padding: 5px 10px;
            border-radius: 5px;
            z-index: 2;
            color: white;
        }

        .lesson-card .row {
            display: flex;
        }

        .lesson-card .col {
            flex: 1;
            padding: 20px;
        }

        .lesson-card img {
            max-width: 100%;
            border-radius: 10px 0 0 10px;
        }

        .lesson-card .profile-section {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .lesson-card .profile-section img {
            height: 80px;
            width: 80px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .lesson-card h3 {
            margin: 0;
        }

        .lesson-card h4 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .lesson-card p {
            font-size: 16px;
        }

        .lesson-materials-card {
            width: 300px;
            height: 250px;
            border: 1px solid #dcdcdc;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            position: relative;
        }

        .lesson-materials-card .card-header {
            padding: 10px;
        }
    </style>
</head>

<body>
    <?php include("misc/navbar.php"); ?>
    <div class="wrapper">
        <?php include("misc/sidebar.php"); ?>
        <div class="main p-3">
            <h1 class="title mx-auto">Lesson Details</h1>
            <hr>
            <div class="lesson-card">
                <?php
                $select_lesson = $conn->prepare("
            SELECT 
                tbl_classlessons.*,
                tbl_section.*,
                tbl_studentclasses.*,
                tbl_lessons.*,
                tbl_teachers.*
            FROM 
                `tbl_classlessons`
            INNER JOIN 
                `tbl_section` ON tbl_classlessons.section_name = tbl_section.section_id
            INNER JOIN 
                `tbl_studentclasses` ON tbl_section.section_id = tbl_studentclasses.section_name
            INNER JOIN 
                `tbl_lessons` ON tbl_classlessons.lesson_id = tbl_lessons.lesson_id
            INNER JOIN 
                `tbl_teachers` ON tbl_teachers.teacher_id = tbl_lessons.teacher_id
            WHERE 
                tbl_classlessons.lesson_id = ?
                AND tbl_lessons.status = 'active' 
                AND tbl_studentclasses.student_id = ?
            LIMIT 1
        ");

                $select_lesson->execute([$get_id, $user_id]);

                if ($select_lesson->rowCount() > 0) {
                    $fetch_lesson = $select_lesson->fetch(PDO::FETCH_ASSOC);

                    $lessons_id = $fetch_lesson['lesson_id'];

                    $count_materials = $conn->prepare("SELECT * FROM `tbl_learningmaterials` WHERE lesson_id = ?");
                    $count_materials->execute([$lessons_id]);
                    $total_materials = $count_materials->rowCount();
                    $fetch_tutor = [
                        'firstname' => $fetch_lesson['firstname'],
                        'lastname' => $fetch_lesson['lastname'],
                        'image' => $fetch_lesson['image'],
                    ];

                    $select_bookmark = $conn->prepare("SELECT * FROM `tbl_bookmark` WHERE user_id = ? AND lesson_id = ?");
                    $select_bookmark->execute([$user_id, $lessons_id]);
                }
                ?>
                <div class="row g-0 g-xl-5 g-xxl-8">
                    <div class="materials-badge text-center">
                        <span>3 Materials</span>
                    </div>
                    <div class="col">
                        <!-- Left section for the image -->
                        <div class="container">
                            <div class="row pt-5 m-auto">
                                <img src="../tmp/<?= $fetch_lesson['thumb']; ?>" alt="lesson_thumbnail" class="img img-fluid rounded-5">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <!-- Right section for the lesson details -->
                        <div class="container">
                            <div class="row pt-5 m-auto">
                                <div class="profile-section mb-4">
                                    <img src="../tmp/<?= $fetch_tutor['image']; ?>" alt="profile_image" class="img img-fluid rounded-10">
                                    <h3><?= $fetch_tutor['firstname']; ?> <?= $fetch_tutor['lastname']; ?></h3>
                                </div>
                                <h4> <?= $fetch_lesson['lesson_title']; ?></h4>
                                <p><?= $fetch_lesson['lesson_desc']; ?></p>

                                <div class="col pt-5 h5">
                                    <i class="bi bi-calendar-fill" style="color: green"></i>
                                    <span class="title">
                                        <?= $fetch_lesson['date']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h1 class="title mx-auto">Lesson Materials</h1>
            <hr>
            <div class="card lesson-materials-card">
                <div class="card-body">
                    <img src="../tmp/<?= $fetch_lesson['thumb']; ?>" alt="lesson_image" class="img img-fluid rounded-5">
                    <h5>Title</h5>
                </div>
            </div>
        </div>
    </div>
</body>

</html>