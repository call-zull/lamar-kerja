<?php
session_start();
// Pastikan pengguna sudah login sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Contoh data proyek mahasiswa
$proyek = [
    ['no' => 1, 'nama_proyek' => 'Proyek A', 'partner' => 'Partner A', 'peran' => 'Developer', 'durasi' => '6 Bulan', 'tujuan_proyek' => 'Meningkatkan skill pemrograman'],
    ['no' => 2, 'nama_proyek' => 'Proyek B', 'partner' => 'Partner B', 'peran' => 'Designer', 'durasi' => '3 Bulan', 'tujuan_proyek' => 'Meningkatkan kreativitas desain']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Proyek Mahasiswa</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .btn-right {
            float: right;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <?php include 'navbar_mhs.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <!-- Success Message -->
                <?php if (!empty($success_message)) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Proyek Mahasiswa</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">Data Proyek Mahasiswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Data Card -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-users"></i>
                                    Data Proyek
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProyekModal">
                                        <i class="fas fa-user-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="proyekTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Proyek</th>
                                                <th>Partner</th>
                                                <th>Peran</th>
                                                <th>Durasi</th>
                                                <th>Tujuan Proyek</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($proyek as $index => $item) { ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo $item['nama_proyek']; ?></td>
                                                    <td><?php echo $item['partner']; ?></td>
                                                    <td><?php echo $item['peran']; ?></td>
                                                    <td><?php echo $item['durasi']; ?></td>
                                                    <td><?php echo $item['tujuan_proyek']; ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm edit-btn" data-index="<?php echo $index; ?>" data-toggle="modal" data-target="#editProyekModal"><i class="fas fa-edit"></i> Edit</button>
                                                        <button class="btn btn-danger btn-sm delete-btn" data-index="<?php echo $index; ?>"><i class="fas fa-trash"></i> Hapus</button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- /.card-body -->
                        </div><!-- /.card -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

<!-- Modal Tambah Proyek -->
<div class="modal fade" id="addProyekModal" tabindex="-1" role="dialog" aria-labelledby="addProyekModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProyekModalLabel">Tambah Proyek</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addProyekForm">
                    <div class="form-group">
                        <label for="nama_proyek">Nama Proyek</label>
                        <input type="text" class="form-control" id="nama_proyek" name="nama_proyek">
                    </div>
                    <div class="form-group">
                        <label for="partner">Partner</label>
                        <input type="text" class="form-control" id="partner" name="partner">
                    </div>
                    <div class="form-group">
                        <label for="peran">Peran</label>
                        <input type="text" class="form-control" id="peran" name="peran">
                    </div>
                    <div class="form-group">
                        <label for="durasi">Durasi</label>
                        <input type="text" class="form-control" id="durasi" name="durasi">
                    </div>
                    <div class="form-group">
                        <label for="tujuan_proyek">Tujuan Proyek</label>
                        <input type="text" class="form-control" id="tujuan_proyek" name="tujuan_proyek">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="addProyek()">Tambah</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Proyek -->
<div class="modal fade" id="editProyekModal" tabindex="-1" role="dialog" aria-labelledby="editProyekModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProyekModalLabel">Edit Proyek</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProyekForm">
                    <input type="hidden" id="editRowIndex">
                    <div class="form-group">
                        <label for="edit_nama_proyek">Nama Proyek</label>
                        <input type="text" class="form-control" id="edit_nama_proyek" name="edit_nama_proyek">
                    </div>
                    <div class="form-group">
                        <label for="edit_partner">Partner</label>
                        <input type="text" class="form-control" id="edit_partner" name="edit_partner">
                    </div>
                    <div class="form-group">
                        <label for="edit_peran">Peran</label>
                        <input type="text" class="form-control" id="edit_peran" name="edit_peran">
                    </div>
                    <div class="form-group">
                        <label for="edit_durasi">Durasi</label>
                        <input type="text" class="form-control" id="edit_durasi" name="edit_durasi">
                    </div>
                    <div class="form-group">
                        <label for="edit_tujuan_proyek">Tujuan Proyek</label>
                        <input type="text" class="form-control" id="edit_tujuan_proyek" name="edit_tujuan_proyek">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="updateProyek()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Proyek -->
<div class="modal fade" id="deleteProyekModal" tabindex="-1" role="dialog" aria-labelledby="deleteProyekModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProyekModalLabel">Hapus Proyek</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus proyek ini?</p>
                <input type="hidden" id="deleteProyekIndex">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteProyek()">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="../../app/plugins/jquery/jquery.min.js"></script>
<script src="../../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    function addProyek() {
        var form = document.getElementById('addProyekForm');
        var table = document.querySelector('table tbody');
        var rowCount = table.rows.length;
        var newRow = table.insertRow(rowCount);

        newRow.insertCell(0).innerHTML = rowCount + 1;
        newRow.insertCell(1).innerHTML = form.nama_proyek.value;
        newRow.insertCell(2).innerHTML = form.partner.value;
        newRow.insertCell(3).innerHTML = form.peran.value;
        newRow.insertCell(4).innerHTML = form.durasi.value;
        newRow.insertCell(5).innerHTML = form.tujuan_proyek.value;
        newRow.insertCell(6).innerHTML = '<button class="btn btn-warning btn-sm edit-btn" data-index="' + rowCount + '" data-toggle="modal" data-target="#editProyekModal"><i class="fas fa-edit"></i> Edit</button>\
        <button class="btn btn-danger btn-sm delete-btn" data-index="' + rowCount + '"><i class="fas fa-trash"></i> Hapus</button>';

        // Clear form fields
        form.nama_proyek.value = '';
        form.partner.value = '';
        form.peran.value = '';
        form.durasi.value = '';
        form.tujuan_proyek.value = '';

        // Close modal
        $('#addProyekModal').modal('hide');
    }

    function updateProyek() {
        var form = document.getElementById('editProyekForm');
        var rowIndex = document.getElementById('editRowIndex').value;
        var table = document.querySelector('table tbody');
        var row = table.rows[rowIndex];

        row.cells[1].innerHTML = form.edit_nama_proyek.value;
        row.cells[2].innerHTML = form.edit_partner.value;
        row.cells[3].innerHTML = form.edit_peran.value;
        row.cells[4].innerHTML = form.edit_durasi.value;
        row.cells[5].innerHTML = form.edit_tujuan_proyek.value;

        // Close modal
        $('#editProyekModal').modal('hide');
    }

    // Event delegation for dynamically created edit and delete buttons
    document.querySelector('table tbody').addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-btn')) {
            var index = e.target.getAttribute('data-index');
            var table = document.querySelector('table tbody');
            var row = table.rows[index];
            
            document.getElementById('editRowIndex').value = index;
            document.getElementById('edit_nama_proyek').value = row.cells[1].innerHTML;
            document.getElementById('edit_partner').value = row.cells[2].innerHTML;
            document.getElementById('edit_peran').value = row.cells[3].innerHTML;
            document.getElementById('edit_durasi').value = row.cells[4].innerHTML;
            document.getElementById('edit_tujuan_proyek').value = row.cells[5].innerHTML;
        }

        if (e.target.classList.contains('delete-btn')) {
            var index = e.target.getAttribute('data-index');
            document.querySelector('table tbody').deleteRow(index);

            // Reorder remaining rows
            var rows = document.querySelectorAll('table tbody tr');
            rows.forEach(function(row, i) {
                row.cells[0].innerHTML = i + 1;
                row.querySelector('.edit-btn').setAttribute('data-index', i);
                row.querySelector('.delete-btn').setAttribute('data-index', i);
            });
        }
    });

    // Ensure buttons inside cell trigger correctly
    document.querySelector('table tbody').addEventListener('click', function(e) {
        if (e.target.tagName === 'I') {
            e.target.closest('button').click();
        }
    });
</script>
</body>
</html>
