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
                <i class="fas fa-moon"></i>
                Dark Mode
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="light-mode-toggle" role="button">
                <i class="fas fa-sun"></i>
                Light Mode
            </a>
        </li>
    </ul>
</nav>


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
                <li class="nav-item">
                    <a href="../index.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../profile_mahasiswa.php" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item has-treeview menu-open">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Data Portofolio <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../crud_sertifikasi/tampil_sertifikasi.php" class="nav-link">
                                <i class="fas fa-award nav-icon"></i>
                                <p>Sertifikasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../crud_lomba/tampil_lomba.php" class="nav-link">
                                <i class="fas fa-trophy nav-icon"></i>
                                <p>Lomba</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../crud_pelatihan/tampil_pelatihan.php" class="nav-link">
                                <i class="fas fa-chalkboard-teacher nav-icon"></i>
                                <p>Pelatihan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../crud_proyek/tampil_proyek.php" class="nav-link">
                                <i class="far fa-folder-open nav-icon"></i>
                                <p>Proyek</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../cari_kerja.php" class="nav-link">
                        <i class="nav-icon fas fa-briefcase"></i>
                        <p>Cari Kerja</p>
                    </a>
                </li>
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
