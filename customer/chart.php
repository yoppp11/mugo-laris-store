<?php
session_start();
include '../config/database.php';

// Tangani aksi hapus produk
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Periksa apakah produk ada di keranjang
    if (isset($_SESSION['keranjang'][$id])) {
        unset($_SESSION['keranjang'][$id]);
    }

    // Jika keranjang kosong, hapus seluruh sesi keranjang
    if (empty($_SESSION['keranjang'])) {
        unset($_SESSION['keranjang']);
    }

    header("Location: chart.php");
    exit();
}

// Tangani aksi tambah produk ke keranjang
if (isset($_GET['action']) && $_GET['action'] === 'tambah' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $nama_produk = $_GET['nama_produk'];
    $harga = $_GET['harga'];

    // Tambahkan produk ke keranjang
    if (!isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id] = [
            'nama_produk' => $nama_produk,
            'jumlah' => 1,
            'harga' => $harga
        ];
    } else {
        $_SESSION['keranjang'][$id]['jumlah']++;
    }

    // Set session notifikasi
    $_SESSION['notifikasi'] = "Produk berhasil ditambahkan ke keranjang!";
    header("Location: chart.php");
    exit();
}

// Cek apakah ada notifikasi
$notifikasi = '';
if (isset($_SESSION['notifikasi'])) {
    $notifikasi = $_SESSION['notifikasi'];
    unset($_SESSION['notifikasi']); // Hapus notifikasi setelah ditampilkan
}

// Cek apakah keranjang kosong
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    echo "<div class='container mt-5'><h3>Keranjang Anda kosong.</h3></div>";
    exit();
}

$total_harga = 0;
?>


<!DOCTYPE html>
<html>
<head>
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Keranjang Belanja</h1>

        <!-- Tampilkan notifikasi jika ada -->
        <?php if ($notifikasi): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $notifikasi; ?>
            </div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['keranjang'] as $id => $item): 
                    $total_harga += $item['jumlah'] * $item['harga'];
                ?>
                <tr>
                    <td><?php echo $item['nama_produk']; ?></td>
                    <td><?php echo $item['jumlah']; ?></td>
                    <td>Rp <?php echo number_format($item['harga'], 2, ',', '.'); ?></td>
                    <td>Rp <?php echo number_format($item['jumlah'] * $item['harga'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="chart.php?action=hapus&id=<?php echo $id; ?>" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Total Harga: Rp <?php echo number_format($total_harga, 2, ',', '.'); ?></h3>
        <a href="checkout.php" class="btn btn-success">Checkout</a>
    </div>
</body>
</html>

