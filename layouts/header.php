<?php $user = find_by_id('users', $_SESSION['user_id']); ?>
<!--begin::Header-->
<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="home.php" class="nav-link">Home</a>
            </li>
        </ul>
        <!--end::Start Navbar Links-->

        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <?php
                    $imagePath = "./uploads/users/" . $user['image'];
                    if (file_exists($imagePath)) {
                        echo '<img class="user-image rounded-circle shadow" src="' . $imagePath . '" alt="User profile picture">';
                    } else {
                        echo '<img class="user-image rounded-circle shadow" src="./uploads/user_default.png" alt="Default profile picture" >';
                    }
                    ?>
                    <span class="d-none d-md-inline"><?php echo $user['username']; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <!--begin::User Image-->
                    <li class="user-header text-bg-warning">
                        <?php
                        $imagePath = "./uploads/users/" . $user['image'];
                        if (file_exists($imagePath)) {
                            echo '<img class="rounded-circle shadow" src="' . $imagePath . '" alt="User profile picture">';
                        } else {
                            echo '<img class="rounded-circle shadow" src="./uploads/user_default.png" alt="Default profile picture" >';
                        }
                        ?>
                        <p>
                            <?php echo $user['name'] . " " . $user['last_name']; ?>
                            <small>Ultimo Sesión <?= $user['last_login'] ?></small>
                        </p>
                    </li>
                    <!--end::User Image-->
                    <!--begin::Menu Footer-->
                    <li class="user-footer">
                        <a href="perfil.php" class="btn btn-default btn-flat">Perfil</a>
                        <a href="logout.php" class="btn btn-default btn-flat float-end">Cerrar Sesión</a>
                    </li>
                    <!--end::Menu Footer-->
                </ul>
            </li>
            <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
</nav>
<!--end::Header-->