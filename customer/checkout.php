<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['keranjang'])) {
    header("Location: login.php");
    exit();
}

$total_harga = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total_harga += $item['jumlah'] * $item['harga'];
}

// Proses checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $query = "INSERT INTO pesanan (user_id, total_harga) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("id", $user_id, $total_harga);
    
    if ($stmt->execute()) {
        $pesanan_id = $stmt->insert_id;

        foreach ($_SESSION['keranjang'] as $id => $item) {
            $query_detail = "INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan) VALUES (?, ?, ?, ?)";
            $stmt_detail = $conn->prepare($query_detail);
            $stmt_detail->bind_param("iiid", $pesanan_id, $id, $item['jumlah'], $item['harga']);
            $stmt_detail->execute();
        }

        // Kosongkan keranjang setelah checkout
        unset($_SESSION['keranjang']);
        header("Location: success.php");
        exit();
    } else {
        $error = "Checkout gagal: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Checkout</h1>
        <h3>Total Harga: Rp <?php echo number_format($total_harga, 2, ',', '.'); ?></h3>
        <form method="POST">
            <button type="submit" class="btn btn-primary">Konfirmasi Pembayaran</button>
        </form>
    </div>
</body>
</html>