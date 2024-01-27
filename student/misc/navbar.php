<?php
$select_profile = $conn->prepare("SELECT * FROM `tbl_students` WHERE id = ?");
$select_profile->execute([$user_id]);

if ($select_profile->rowCount() > 0) {
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

    $_SESSION['firstname'] = $fetch_profile['firstname'];
    $_SESSION['lastname'] = $fetch_profile['lastname'];
    $_SESSION['image'] = $fetch_profile['image'];
    $_SESSION['sid'] = $fetch_profile['id'];
} else {
    header("location: ../index.php");
}
?>


<nav class="navbar  navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="#">PHINMA COC</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="form-check form-switch form-switch-lg ms-auto">
                <div class="check-box">
                    <input type="checkbox">
                </div>
            </div>
        </div>
    </div>
</nav>