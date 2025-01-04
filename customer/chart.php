<?php
session_start();
include '../config/database.php';

if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if (isset($_SESSION['keranjang'][$id])) {
        unset($_SESSION['keranjang'][$id]);
    }

    if (empty($_SESSION['keranjang'])) {
        unset($_SESSION['keranjang']);
    }

    header("Location: chart.php");
    exit();
}

if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    echo "<div class='container mt-5 text-center'><h3>Keranjang Anda kosong.</h3></div>";
    exit();
}

$total_harga = 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link rel="stylesheet" href="chart-style.css"> -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #608dee;
            max-height: 50px;
        }

        .navbar .navbar-brand {
            color: white;
            font-weight: 600;
            font-size: 24px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            letter-spacing: 2px;
            margin-left: 8px;
        }

        .nav-item {
            font-weight: 500;
        }

        .nav-item .nav-link {
            color: white;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .btn-danger {
            background-color: #ff6f61;
            border-color: #ff6f61;
        }

        .btn-danger:hover {
            background-color: #e0554e;
            border-color: #e0554e;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
        }

        .total-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a href="javascript:history.back()" class="btn btn-light me-2">
                <i class="fas fa-arrow-left"></i> <!-- Ikon Kembali -->
            </a>
            <a class="navbar-brand" href="../index.php">MugoLaris</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./gallery.php">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="./chart.php">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../about_us.html">Tentang Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Keranjang Belanja</h1>
        <table class="table table-bordered table-hover">
            <thead class="table-secondary">
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

        <div class="total-card">
            <h4>Total Harga: <span class="text-primary">Rp
                    <?php echo number_format($total_harga, 2, ',', '.'); ?></span></h4>
            <a href="checkout.php" class="btn btn-success mt-3">Checkout</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>