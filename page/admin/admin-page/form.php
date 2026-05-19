<?php
$koneksi = new mysqli("localhost", "root", "", "dior");

// --- VAR UNTUK NOTIFIKASI ---
$notifikasi = "";

// --- HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM tas WHERE ID_tas = '$id'");
    $notifikasi = "<div class='alert alert-warning text-center fade-alert'>
        Data tas dengan ID <b>$id</b> berhasil dihapus!
    </div>";
}

// --- AMBIL DATA UNTUK EDIT ---
$edit_mode = false;
$edit_id = "";
$edit_nama = "";

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $data_edit = $koneksi->query("SELECT * FROM tas WHERE ID_tas = '$edit_id'");
    if ($data_edit->num_rows > 0) {
        $row = $data_edit->fetch_assoc();
        $edit_mode = true;
        $edit_nama = $row['nama_tas'];
    }
}

// --- SIMPAN DATA (TAMBAH / UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ID_tas = trim($_POST['ID_tas']);
    $Nama   = trim($_POST['Nama']);

    if (!empty($ID_tas) && !empty($Nama)) {
        if (isset($_POST['update'])) {
            $old_id = $_POST['old_id'];
            $koneksi->query("UPDATE tas SET ID_tas='$ID_tas', nama_tas='$Nama' WHERE ID_tas='$old_id'");
            $notifikasi = "<div class='alert alert-info text-center fade-alert'>Data tas berhasil diperbarui!</div>";
        } else {
            $cek = $koneksi->query("SELECT * FROM tas WHERE ID_tas='$ID_tas'");
            if ($cek->num_rows > 0) {
                $notifikasi = "<div class='alert alert-danger text-center fade-alert'>
                    ID Tas <b>$ID_tas</b> sudah ada! Gunakan ID lain.
                </div>";
            } else {
                $koneksi->query("INSERT INTO tas (ID_tas, nama_tas) VALUES ('$ID_tas', '$Nama')");
                $notifikasi = "<div class='alert alert-success text-center fade-alert'>
                    Data tas berhasil disimpan!
                </div>";
            }
        }
    }
}

// --- AMBIL SEMUA DATA TAS ---
$data_tas = $koneksi->query("SELECT * FROM tas ORDER BY ID_tas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Tas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .fade-alert { opacity: 1; transition: opacity 1s ease-out; }
    .fade-alert.hide { opacity: 0; }
  </style>
  <script>
    function konfirmasiHapus(id) {
      if (confirm('Yakin ingin menghapus data dengan ID ' + id + '?')) {
        window.location = '?page=form&hapus=' + id;
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      const alertBox = document.querySelector(".fade-alert");
      if (alertBox) {
        setTimeout(() => {
          alertBox.classList.add("hide");
          setTimeout(() => alertBox.remove(), 800);
        }, 1500);
      }
    });
  </script>
</head>

<body class="bg-light">
  <div class="container mt-4">

    <!-- Notifikasi -->
    <?php if (!empty($notifikasi)) echo $notifikasi; ?>

    <div class="card shadow-lg p-4 rounded-4 mb-4">
      <h3 class="mb-4 text-center"><?= $edit_mode ? 'Edit Data Tas' : 'Tambah Tas' ?></h3>

      <form action="?page=form" method="POST">
        <div class="mb-3">
          <label class="form-label">ID Tas</label>
          <input type="text" name="ID_tas" class="form-control"
                 value="<?= $edit_mode ? htmlspecialchars($edit_id) : '' ?>"
                 placeholder="Masukkan ID tas" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Nama Tas</label>
          <textarea name="Nama" class="form-control" rows="3"
                    placeholder="Tuliskan Nama Tas" required><?= $edit_mode ? htmlspecialchars($edit_nama) : '' ?></textarea>
        </div>

        <?php if ($edit_mode): ?>
          <input type="hidden" name="old_id" value="<?= htmlspecialchars($edit_id) ?>">
          <button type="submit" name="update" class="btn btn-warning w-100">Update Data</button>
          <a href="?page=form" class="btn btn-secondary w-100 mt-2">Batal Edit</a>
        <?php else: ?>
          <button type="submit" class="btn btn-primary w-100">Simpan</button>
        <?php endif; ?>
      </form>
    </div>

    <div class="card shadow-lg p-4 rounded-4">
      <h4 class="mb-3 text-center">Daftar Tas</h4>
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark text-center">
          <tr>
            <th>No</th>
            <th>ID Tas</th>
            <th>Nama Tas</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          if ($data_tas->num_rows > 0) {
              while ($row = $data_tas->fetch_assoc()) {
                  echo "<tr>
                          <td class='text-center'>$no</td>
                          <td class='text-center'>{$row['ID_tas']}</td>
                          <td class='text-center'>{$row['nama_tas']}</td>
                          <td class='text-center'>
                            <a href='?page=form&edit={$row['ID_tas']}' class='btn btn-warning btn-sm me-1'>Edit</a>
                            <button class='btn btn-danger btn-sm' onclick=\"konfirmasiHapus('{$row['ID_tas']}')\">Hapus</button>
                          </td>
                        </tr>";
                  $no++;
              }
          } else {
              echo "<tr><td colspan='4' class='text-center text-muted'>Belum ada data tas.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
