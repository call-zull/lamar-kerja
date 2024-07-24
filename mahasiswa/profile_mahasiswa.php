<?php
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

// Fetch student profile information
$user_id = $_SESSION['user_id'];
$sql = "SELECT m.id, m.nim, m.nama_mahasiswa, m.prodi_id, m.jurusan_id, p.nama_prodi, j.nama_jurusan, m.tahun_masuk, m.status, m.jk, m.alamat, m.email, m.no_telp, m.profile_image, m.keahlian,
               m.ipk_semester_1, m.ipk_semester_2, m.ipk_semester_3, m.ipk_semester_4, m.ipk_semester_5, m.ipk_semester_6, m.ipk_semester_7, m.ipk_semester_8
        FROM mahasiswas m
        LEFT JOIN prodis p ON m.prodi_id = p.id
        LEFT JOIN jurusans j ON m.jurusan_id = j.id
        WHERE m.user_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// Check if student data is found
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profile) {
    die("Data mahasiswa tidak ditemukan.");
}

// Calculate cumulative IPK
$ipk_values = [
    $profile['ipk_semester_1'],
    $profile['ipk_semester_2'],
    $profile['ipk_semester_3'],
    $profile['ipk_semester_4'],
    $profile['ipk_semester_5'],
    $profile['ipk_semester_6'],
    $profile['ipk_semester_7'],
    $profile['ipk_semester_8']
];

$ipk_total = 0;
$ipk_count = 0;
foreach ($ipk_values as $ipk) {
    if ($ipk !== null) {
        $ipk_total += $ipk;
        $ipk_count++;
    }
}
$cumulative_ipk = $ipk_count > 0 ? round($ipk_total / $ipk_count, 2) : 0;

// Fetch all jurusan and prodi options for dropdowns
$sql_jurusans = "SELECT * FROM jurusans";
$stmt_jurusans = $pdo->prepare($sql_jurusans);
$stmt_jurusans->execute();
$jurusans = $stmt_jurusans->fetchAll(PDO::FETCH_ASSOC);

$sql_prodis = "SELECT * FROM prodis";
$stmt_prodis = $pdo->prepare($sql_prodis);
$stmt_prodis->execute();
$prodis = $stmt_prodis->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Mahasiswa</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }

        .btn-bottom-left {
            position: fixed;
            bottom: 20px;
            left: 20px;
        }

        .profile-img {
            max-width: 200px;
            max-height: 200px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: #f3f2ef;
            border-bottom: 1px solid #dcdcdc;
        }

        .profile-header img {
            border-radius: 50%;
            margin-right: 20px;
        }

        .profile-header h1 {
            margin: 0;
            font-size: 22px;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 18px;
            color: #666;
        }

        .profile-content {
            padding: 20px;
        }

        .profile-section {
            margin-bottom: 20px;
        }

        .profile-section h3 {
            border-bottom: 1px solid #dcdcdc;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .profile-section .form-group {
            margin-bottom: 15px;
        }

        .profile-section .form-control {
            border-radius: 5px;
        }

        .keahlian-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-left: auto;
        }

        .keahlian-container .btn {
            margin-left: 10px;
            margin-bottom: 10px;
        }

        .keahlian-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .remove-keahlian-btn {
            color: white;
            background-color: red;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Nav bar -->
        <?php include 'navbar_mhs.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Profile Mahasiswa</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Profile Mahasiswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <section class="col-lg-12 connectedSortable">
                            <div class="card">
                                <div class="profile-header">
                                    <?php if (!empty($profile['profile_image'])) : ?>
                                        <img src="../assets/mahasiswa/profile/<?= htmlspecialchars($profile['profile_image']) ?>" class="img-fluid profile-img" alt="Mahasiswa Image">
                                    <?php else : ?>
                                        <img src="../assets/images/profile_default.png" class="img-fluid profile-img" alt="Default Image">
                                    <?php endif; ?>
                                    <div>
                                        <h2>IPK: <?= $cumulative_ipk ?></h2>
                                        <h1><?= htmlspecialchars($profile['nama_mahasiswa']) ?></h1>
                                        <h2><?= htmlspecialchars($profile['nama_prodi']) ?>, <?= htmlspecialchars($profile['nama_jurusan']) ?></h2>
                                    </div>
                                    <div class="keahlian-container d-flex flex-wrap">
                                        <button type="button" class="btn btn-primary ml-2" id="edit-profile-btn">
                                            <i class="fas fa-edit"></i> Edit Informasi
                                        </button>
                                        <button type="button" class="btn btn-success d-none ml-2" id="save-profile-btn">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" id="add-keahlian-btn">
                                            <i class="fas fa-plus"></i> Tambah Keahlian
                                        </button>
                                        <button type="button" class="btn btn-warning ml-2" id="add-ipk-btn">
                                            <i class="fas fa-plus"></i> Tambah IPK
                                        </button>
                                        <button type="button" class="btn btn-info ml-2" id="change-photo-btn" data-toggle="modal" data-target="#changePhotoModal">
                                            <i class="fas fa-camera"></i> Ubah Foto
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body profile-content">
                                    <form id="profile-form" action="update_profile.php" method="post">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($profile['id']) ?>">
                                        <input type="hidden" id="hidden-keahlian" name="keahlian" value="<?= htmlspecialchars($profile['keahlian']) ?>">
                                        <div class="profile-section">
                                            <h3>Informasi Dasar</h3>
                                            <div class="form-group">
                                                <label for="nim">NIM</label>
                                                <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($profile['nim']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="nama">Nama</label>
                                                <input type="text" class="form-control profile-input" id="nama" name="nama_mahasiswa" value="<?= htmlspecialchars($profile['nama_mahasiswa']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="jurusan">Jurusan</label>
                                                <select class="form-control profile-input" id="jurusan" name="jurusan_id" readonly>
                                                    <?php foreach ($jurusans as $jurusan) : ?>
                                                        <option value="<?= htmlspecialchars($jurusan['id']) ?>" <?= ($jurusan['id'] == $profile['jurusan_id']) ? 'selected' : '' ?>><?= htmlspecialchars($jurusan['nama_jurusan']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="prodi">Prodi</label>
                                                <select class="form-control profile-input" id="prodi" name="prodi_id" readonly>
                                                    <?php foreach ($prodis as $prodi) : ?>
                                                        <option value="<?= htmlspecialchars($prodi['id']) ?>" <?= ($prodi['id'] == $profile['prodi_id']) ? 'selected' : '' ?>><?= htmlspecialchars($prodi['nama_prodi']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="tahun_masuk">Tahun Masuk</label>
                                                <input type="number" class="form-control profile-input" id="tahun_masuk" name="tahun_masuk" value="<?= htmlspecialchars($profile['tahun_masuk']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control profile-input" id="status" name="status" readonly>
                                                    <option value="mahasiswa aktif" <?= ($profile['status'] == 'mahasiswa aktif') ? 'selected' : '' ?>>Mahasiswa Aktif</option>
                                                    <option value="alumni" <?= ($profile['status'] == 'alumni') ? 'selected' : '' ?>>Alumni</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="jk">Jenis Kelamin</label>
                                                <select class="form-control profile-input" id="jk" name="jk" readonly>
                                                    <option value="laki-laki" <?= ($profile['jk'] == 'laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                                    <option value="perempuan" <?= ($profile['jk'] == 'perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="profile-section">
                                            <h3>Informasi Kontak</h3>
                                            <div class="form-group">
                                                <label for="alamat">Alamat</label>
                                                <input type="text" class="form-control profile-input" id="alamat" name="alamat" value="<?= htmlspecialchars($profile['alamat']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control profile-input" id="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">No. HP</label>
                                                <input type="tel" class="form-control profile-input" id="no_hp" name="no_telp" value="<?= htmlspecialchars($profile['no_telp']) ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="profile-section">
                                            <h3>Keahlian</h3>
                                            <div id="keahlian-list">
                                                <?php
                                                $keahlian_list = explode(',', $profile['keahlian']);
                                                foreach ($keahlian_list as $keahlian) {
                                                    if (!empty($keahlian)) {
                                                        echo '<div class="form-group keahlian-item"><input type="text" class="form-control" value="' . htmlspecialchars($keahlian) . '" readonly>';
                                                        echo '<button type="button" class="btn btn-danger btn-sm remove-keahlian-btn" data-skill="' . htmlspecialchars($keahlian) . '"><i class="fas fa-trash-alt"></i></button></div>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal Tambah Keahlian -->
    <div class="modal fade" id="addKeahlianModal" tabindex="-1" role="dialog" aria-labelledby="addKeahlianModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="add-keahlian-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addKeahlianModalLabel">Tambah Keahlian</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new-keahlian">Keahlian</label>
                            <input type="text" class="form-control" id="new-keahlian" name="new_keahlian" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Deleting Skill -->
    <div class="modal fade" id="deleteSkillModal" tabindex="-1" role="dialog" aria-labelledby="deleteSkillModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteSkillModalLabel">Hapus Keahlian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus keahlian ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-skill-btn">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ubah Foto -->
    <div class="modal fade" id="changePhotoModal" tabindex="-1" role="dialog" aria-labelledby="changePhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="change-photo-form" action="update_photo.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePhotoModalLabel">Ubah Foto Profil</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="profile_image">Pilih Foto Baru</label>
                            <input type="file" class="form-control-file" id="profile_image" name="profile_image" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- IPK -->
 
<div class="modal fade" id="addIpkModal" tabindex="-1" role="dialog" aria-labelledby="addIpkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="add-ipk-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="addIpkModalLabel">Tambah IPK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="ipk-notification" class="alert alert-success d-none" role="alert"></div> <!-- Notifikasi -->
                    <p>*Inputkan berdasarkan nilai Indeks Prestasi terakhir Anda</p>
                    <?php for ($i = 1; $i <= 8; $i++) : ?>
                        <div class="form-group row">
                            <label for="ipk_semester_<?= $i ?>" class="col-sm-4 col-form-label">IPK Semester <?= $i ?></label>
                            <div class="col-sm-4">
                                <input type="number" step="0.01" min="0" max="4" class="form-control" id="ipk_semester_<?= $i ?>" name="ipk_semester_<?= $i ?>" value="<?= htmlspecialchars($profile["ipk_semester_$i"]) ?>">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-primary btn-sm save-ipk" data-semester="<?= $i ?>">Simpan</button>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger btn-sm delete-ipk" data-semester="<?= $i ?>">Hapus</button>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const saveProfileBtn = document.getElementById('save-profile-btn');
        const addKeahlianBtn = document.getElementById('add-keahlian-btn');
        const addIpkBtn = document.getElementById('add-ipk-btn');
        const formElements = document.querySelectorAll('.profile-input');
        const statusElement = document.getElementById('status');
        const jkElement = document.getElementById('jk');
        const keahlianList = document.getElementById('keahlian-list');
        let skillToDelete = '';

        editProfileBtn.addEventListener('click', function() {
            editProfileBtn.classList.add('d-none');
            saveProfileBtn.classList.remove('d-none');
            enableEditing();
        });

        saveProfileBtn.addEventListener('click', function() {
            document.getElementById('profile-form').submit();
        });

        function enableEditing() {
            formElements.forEach(element => {
                element.removeAttribute('readonly');
            });
            statusElement.removeAttribute('readonly');
            jkElement.removeAttribute('readonly');
            document.querySelectorAll('.remove-keahlian-btn').forEach(btn => {
                btn.classList.remove('d-none');
            });
        }

        addKeahlianBtn.addEventListener('click', function() {
            $('#addKeahlianModal').modal('show');
        });

        addIpkBtn.addEventListener('click', function() {
            $('#addIpkModal').modal('show');
        });

        document.getElementById('add-keahlian-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const newKeahlian = document.getElementById('new-keahlian').value;

            const newKeahlianDiv = document.createElement('div');
            newKeahlianDiv.className = 'form-group keahlian-item';
            newKeahlianDiv.innerHTML = `<input type="text" class="form-control" value="${newKeahlian}" readonly>`;
            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-danger btn-sm remove-keahlian-btn';
            removeBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
            newKeahlianDiv.appendChild(removeBtn);
            keahlianList.appendChild(newKeahlianDiv);

            updateHiddenKeahlianInput();
            saveSkills();

            $('#addKeahlianModal').modal('hide');
        });

        document.querySelectorAll('.save-ipk').forEach(button => {
        button.addEventListener('click', function() {
            const semester = this.getAttribute('data-semester');
            const ipkValue = document.getElementById('ipk_semester_' + semester).value;
            saveIpk(semester, ipkValue);
        });
});

document.querySelectorAll('.delete-ipk').forEach(button => {
    button.addEventListener('click', function() {
        const semester = this.getAttribute('data-semester');
        deleteIpk(semester);
    });
});

function saveIpk(semester, value) {
    $.ajax({
        type: 'POST',
        url: 'update_ipk.php',
        data: { semester: semester, value: value },
        success: function(response) {
            calculateIpk();
            showNotification(`IPK semester ${semester} berhasil disimpan.`);
        }
    });
}

function deleteIpk(semester) {
    $.ajax({
        type: 'POST',
        url: 'delete_ipk.php',
        data: { semester: semester },
        success: function(response) {
            document.getElementById('ipk_semester_' + semester).value = '';
            calculateIpk();
            showNotification('IPK semester ' + semester + ' berhasil dihapus.');
        }
    });
}

function showNotification(message) {
    const notification = document.getElementById('ipk-notification');
    notification.innerText = message;
    notification.classList.remove('d-none');
    notification.classList.add('d-block');
    setTimeout(() => {
        notification.classList.remove('d-block');
        notification.classList.add('d-none');
    }, 1500);
}

function calculateIpk() {
    let total = 0;
    let count = 0;
    for (let i = 1; i <= 8; i++) {
        const value = parseFloat(document.getElementById('ipk_semester_' + i).value);
        if (!isNaN(value) && value > 0) {
            total += value;
            count++;
        }
    }
    const ipk = (count > 0) ? (total / count).toFixed(2) : 0;
    // document.getElementById('cumulative-ipk').innerText = `IPK: ${ipk}`;

    // Save IPK to the database
    $.ajax({
        type: 'POST',
        url: 'update_ipk_overall.php',
        data: { ipk: ipk },
        success: function(response) {
            console.log(response);
        }
    });
}




        // function showNotification(message) {
        //     const notification = document.createElement('div');
        //     notification.className = 'alert alert-success';
        //     notification.role = 'alert';
        //     notification.innerText = message;
        //     document.body.appendChild(notification);

        //     setTimeout(() => {
        //         console.log('testing')
        //         notification.remove();
        //     }, 3000);
        // }

        keahlianList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-keahlian-btn') || e.target.parentElement.classList.contains('remove-keahlian-btn')) {
                skillToDelete = e.target.getAttribute('data-skill') || e.target.parentElement.getAttribute('data-skill');
                $('#deleteSkillModal').modal('show');
            }
        });

        document.getElementById('confirm-delete-skill-btn').addEventListener('click', function() {
            deleteSkill(skillToDelete);
            $('#deleteSkillModal').modal('hide');
        });

        function deleteSkill(skill) {
            document.querySelectorAll('.keahlian-item').forEach(item => {
                if (item.querySelector('input').value === skill) {
                    item.remove();
                }
            });
            updateHiddenKeahlianInput();
            saveSkills();
        }

        function updateHiddenKeahlianInput() {
            const keahlianArray = [];
            document.querySelectorAll('.keahlian-item input').forEach(input => {
                keahlianArray.push(input.value);
            });
            document.getElementById('hidden-keahlian').value = keahlianArray.join(',');
        }

        function saveSkills() {
            const keahlian = document.getElementById('hidden-keahlian').value;
            $.ajax({
                type: 'POST',
                url: 'update_skills.php',
                data: { keahlian },
                success: function(response) {
                    console.log(response);
                }
            });
        }
    </script>
</body>

</html>
