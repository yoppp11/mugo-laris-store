<?php
session_start();
include '../config/database.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Periksa apakah folder 'uploads/' sudah ada
$target_dir = __DIR__ . "/uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true); // Buat folder dengan izin tulis
}

// Proses tambah produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];
    $gambar = '';

    // Upload gambar jika ada
    if (!empty($_FILES['gambar']['name'])) {
        $file_name = basename($_FILES['gambar']['name']);
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_size = $_FILES['gambar']['size'];
        $file_type = mime_content_type($file_tmp);
    
        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file_type, $allowed_types)) {
            $error = "Hanya file gambar (JPEG, PNG, GIF) yang diperbolehkan.";
        } elseif ($file_size > 2 * 1024 * 1024) { // Maksimal 2MB
            $error = "Ukuran file maksimal adalah 2MB.";
        } else {
            $unique_name = uniqid() . "_" . $file_name; // Nama file unik
            $gambar = "uploads/" . $unique_name;
    
            if (move_uploaded_file($file_tmp, $target_dir . $unique_name)) {
                // Berhasil diunggah
            } else {
                $error = "Gagal mengunggah gambar. Silakan coba lagi.";
            }
        }
    }

    // Masukkan data ke database
    $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, kategori, gambar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdiis", $nama_produk, $deskripsi, $harga, $stok, $kategori, $gambar);

    if ($stmt->execute()) {
        // Tampilkan JavaScript untuk pesan sukses dan navigasi
        echo "<script>
            alert('Produk berhasil ditambahkan!');
            window.location.href = 'dashboard.php';
            const closeTabs = window.open('', '_self');
            closeTabs.close();
        </script>";
        exit(); // Keluar setelah memproses
    } else {
        $error = "Gagal menambahkan produk. Silakan coba lagi.";
    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Fungsi untuk menampilkan dialog konfirmasi
        function confirmSubmission(event) {
            event.preventDefault(); // Mencegah pengiriman form secara langsung
            const confirmed = confirm("Apakah Anda yakin ingin menambahkan produk ini?");
            if (confirmed) {
                document.getElementById("productForm").submit(); // Submit form jika admin memilih "OK"
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Tambah Produk</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" id="productForm" class="mt-4">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" required>
            </div>
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <input type="text" class="form-control" id="kategori" name="kategori">
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Upload Gambar</label>
                <input type="file" class="form-control" id="gambar" name="gambar">
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-primary" onclick="confirmSubmission(event)">Simpan</button>
                <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

