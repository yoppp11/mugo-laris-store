<?php
session_start();
include '../belajar-bootstrap/config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Ambil data produk terbaru
$query_produk = "SELECT * FROM produk ORDER BY created_at DESC LIMIT 3"; // Ganti 'produk' dengan nama tabel yang sesuai
$stmt_produk = $conn->prepare($query_produk);
$stmt_produk->execute();
$result_produk = $stmt_produk->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Produk</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <!-- Custom Styles -->

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar mb-4 shadow-sm">
        <div class="container">
            <a style="color: white" class="navbar-brand" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./customer/gallery.php">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./customer/chart.php">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_us.html">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./customer/login.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">

        <!-- Carousel Slider -->
        <div id="carouselIklan" class="carousel slide mb-5" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselIklan" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselIklan" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselIklan" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="promo-poster">
                        <div class="promo-content">
                            <h1>Special Offer!</h1>
                            <p>Dapatkan diskon hingga 50% untuk pembelian pertama Anda. Promo berlaku minggu ini!</p>
                            <a href="#" class="promo-button">Beli Sekarang</a>
                        </div>
                        <!-- <div class="promo-image">
                            <img src="https://via.placeholder.com/800x400" alt="Promo Image">
                        </div> -->
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://i.ytimg.com/vi/d2GamfrpjQQ/maxresdefault.jpg" class="d-block w-100" alt="Iklan 2">
                </div>
                <div class="carousel-item">
                    <img src="https://i.ytimg.com/vi/d2GamfrpjQQ/maxresdefault.jpg" class="d-block w-100" alt="Iklan 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselIklan" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselIklan" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>


        <!-- Welcome Section -->
        <div class="row welcome-section mb-4">
            <div class="col-12">
                <h1 style="color: white" class="h3">Selamat Datang,
                    <?php echo htmlspecialchars($user['nama_lengkap']); ?></h1>
                <p style="color: white" class="text-muted">Berikut adalah produk terbaru kami</p>
            </div>
        </div>

        <!-- Products Section -->
        <div class="product-section">
            <div class="row">
                <div class="col-12 mb-3">
                    <h2 class="h4">Produk Terbaru</h2>
                </div>

                <?php while ($produk = $result_produk->fetch_assoc()): ?>
                    <div class="col-12 col-md-4 col-lg-3 mb-4">
                        <div class="card card-product h-100 shadow-sm">
                            <img src="./admin/uploads/<?php echo htmlspecialchars(basename($produk['gambar'])); ?>"
                                class="card-img-top" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>"
                                height="200px" style="object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($produk['nama_produk']); ?></h5>
                                <p class="card-text text-muted">
                                    Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                                </p>
                                <a href="./customer/detail_produk.php?id=<?php echo $produk['id']; ?>"
                                    class="btn btn-primary btn-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h3>Information</h3>
                    <ul class="list-unstyled">
                        <li>
                            <a class="link-footer" href="about_us.html" style="color: black; text-decoration: none;">About Us</a>
                         </li>
                        <li>Terms & Conditions</li>
                        <li>Shipping Policy</li>
                        <li>Return & Exchange</li>
                        <li>Advertising</li>
                        <li>Affiliate Program</li>
                        <li>Wholesale</li>
                        <li>Write to Us</li>
                        <li>Customer Helpline / Hotline</li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h3>Categories</h3>
                    <ul class="list-unstyled">
                        <li>New Release</li>
                        <li>Pre-Orders</li>
                        <li>Beginners</li>
                        <li>Payment Method</li>
                        <li>Delivery By</li>
                        <li>Store Location</li>
                        <li>World Secure</li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h3>Contact Us</h3>
                    <p>Monday - Sunday (09:00 - 17:00)</p>
                    <p>Phone Number</p>
                    <p>Email</p>
                    <p>WhatsApp</p>
                    <p>Order Method</p>
                    <p>Address</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS dan Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>