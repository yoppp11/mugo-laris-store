<?php
session_start();
include '../config/database.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus produk dari database
    $query_hapus = "DELETE FROM produk WHERE id = ?";
    $stmt = $conn->prepare($query_hapus);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus produk!'); window.location.href='dashboard.php';</script>";
    }

    $stmt->close();
    exit();
}

// Ambil statistik
$query_produk = "SELECT COUNT(*) as total_produk FROM produk";
$query_pesanan = "SELECT COUNT(*) as total_pesanan FROM pesanan";

$result_produk = $conn->query($query_produk);
$result_pesanan = $conn->query($query_pesanan);

$total_produk = $result_produk->fetch_assoc()['total_produk'];
$total_pesanan = $result_pesanan->fetch_assoc()['total_pesanan'];

// Ambil daftar produk
$query_daftar_produk = "SELECT id, nama_produk, harga, stok, kategori FROM produk ORDER BY created_at DESC LIMIT 10";
$result_daftar_produk = $conn->query($query_daftar_produk);
$produk_list = $result_daftar_produk->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_admin_dashboard.css">
    <style>
        
    </style>
</head>
<body>
    <header>
        <h1>Dashboard Admin</h1>
    </header>
    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $total_produk; ?></h3>
                <p>Total Produk</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_pesanan; ?></h3>
                <p>Total Pesanan</p>
            </div>
        </div>
        <h2>Daftar Produk</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produk_list as $produk): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                        <td>Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $produk['stok']; ?></td>
                        <td><?php echo htmlspecialchars($produk['kategori']); ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $produk['id']; ?>">Edit</a> | 
                            <a href="dashboard.php?action=hapus&id=<?php echo $produk['id']; ?>" onclick="return confirm('Hapus produk ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav> 
            <a href="manage_order.php">Kelola Pesanan</a>
            <a href="add_product.php">Tambah Produk</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
</body>
</html>
