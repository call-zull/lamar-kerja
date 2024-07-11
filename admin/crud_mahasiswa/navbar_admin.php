<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">SI Portofolio</a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="#" id="dark-mode-toggle" role="button">
                <i class="fas fa-moon"></i> Dark Mode
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="light-mode-toggle" role="button">
                <i class="fas fa-sun"></i> Light Mode
            </a>
        </li>
    </ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="../../app/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SI Portofolio</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="../index.php" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- Profile -->
                <li class="nav-item">
                    <a href="../profile_admin.php" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                 <!-- Registrasi -->
                 <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            Registrasi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../registrasi_mahasiswa.php" class="nav-link">
                                <i class="fas fa-user-edit nav-icon"></i>
                                <p>Mahasiswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../registrasi_perusahaan.php" class="nav-link">
                                <i class="fas fa-building nav-icon"></i>
                                <p>Perusahaan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- User -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>
                            User
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../crud_admin/tampil_user_admin.php" class="nav-link">
                                <i class="fas fa-user-shield nav-icon"></i>
                                <p>Admin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../crud_mahasiswa/tampil_user_mahasiswa.php" class="nav-link">
                                <i class="fas fa-user-graduate nav-icon"></i>
                                <p>Mahasiswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../crud_cdc/tampil_user_cdc.php" class="nav-link">
                                <i class="fas fa-user-tie nav-icon"></i>
                                <p>CDC</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../crud_perusahaan/tampil_user_perusahaan.php" class="nav-link">
                                <i class="fas fa-building nav-icon"></i>
                                <p>Perusahaan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Portofolio Mahasiswa -->
                <li class="nav-item">
                    <a href="../data_portofolio/portofolio_mhs.php" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Data Portofolio</p>
                    </a>
                </li>
                <!-- Lowker -->
                <li class="nav-item">
                    <a href="../perusahaan_admin.php" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Lowker</p>
                    </a>
                </li>
                <!-- Logout -->
                <li class="nav-item">
                    <a href="../../auth/logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
