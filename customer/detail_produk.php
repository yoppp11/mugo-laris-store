<?php
session_start();
include '../config/database.php';

// Cek apakah ID produk ada
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$produk_id = $_GET['id'];

// Ambil detail produk
$query = "SELECT * FROM produk WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $produk_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$produk = $result->fetch_assoc();

// Proses tambah ke keranjang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_keranjang'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $jumlah = $_POST['jumlah'];

    // Pastikan keranjang ada di sesi
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Tambah atau perbarui produk di keranjang
    if (isset($_SESSION['keranjang'][$produk_id])) {
        $_SESSION['keranjang'][$produk_id]['jumlah'] += $jumlah;
    } else {
        $_SESSION['keranjang'][$produk_id] = [
            'nama_produk' => $produk['nama_produk'],
            'jumlah' => $jumlah,
            'harga' => $produk['harga']
        ];
    }

    // Set session notifikasi
    $_SESSION['notifikasi'] = "Produk berhasil ditambahkan ke keranjang!";
    header("Location: detail_produk.php?id=$produk_id&success=1");
    exit();
}

// Cek apakah ada notifikasi
$notifikasi = '';
if (isset($_SESSION['notifikasi'])) {
    $notifikasi = $_SESSION['notifikasi'];
    unset($_SESSION['notifikasi']); // Hapus notifikasi setelah ditampilkan
}
?>


<!DOCTYPE html>
<html>
<head>
    <title><?php echo $produk['nama_produk']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Tampilkan notifikasi jika ada -->
        <?php if ($notifikasi): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $notifikasi; ?>
            </div>
        <?php endif; ?>

        <h1><?php echo $produk['nama_produk']; ?></h1>
        <img src="../admin/uploads/<?php echo htmlspecialchars(basename($produk['gambar'])); ?>" 
             alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" 
             class="img-fluid mb-3">
        <p><?php echo $produk['deskripsi']; ?></p>
        <p>Harga: Rp <?php echo number_format($produk['harga'], 2, ',', '.'); ?></p>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Jumlah</label>
                <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
            </div>
            <button type="submit" name="tambah_keranjang" class="btn btn-primary">Tambah ke Keranjang</button>
        </form>
    </div>
    <script>
        // Jika URL mengandung parameter success=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            // Hapus parameter success agar tidak muncul lagi
            history.replaceState(null, '', window.location.pathname);

            // Deteksi jika pengguna menekan tombol back
            window.addEventListener('popstate', function () {
                // Arahkan ke halaman utama dan tutup tab ini
                window.location.href = 'index.php';
                window.close();
            });
        }
    </script>
</body>
</html>
