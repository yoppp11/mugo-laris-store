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
    <style>
        .custom-navbar {
            background-color: #608dee;
            color: white;
            max-height: 50px;
        }

        .custom-navbar .nav-link {
            color: white;
        }

        .custom-navbar .nav-link:hover {
            color: #FFC300;
            /* Warna saat hover */
        }

        .custom-navbar .navbar-brand {
            color: white !important;
            font-weight: 600 !important;
            font-size: 24px !important;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif !important;
            letter-spacing: 2px !important;
        }

        .product-section {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 24px;
        }

        body {
            background-color: #ffff;
        }

        .top-nav {
            background-color: #DC1E2D;
        }

        .nav-link {
            color: white;
        }

        .link-footer {
            color: black;
            text-decoration: none;
            font-style: normal;
        }

        .link-footer:hover {
            color: #FFC300;
        }

        .search-input {
            width: 400px;
        }

        @media (max-width: 768px) {
            .search-input {
                width: 100%;
            }
        }

        .promo-banner {
            background: linear-gradient(45deg, #FF69B4, #FF1493);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .clock-icon {
            background-color: #4169E1;
            padding: 10px;
            border-radius: 50%;
            color: white;
        }

        .percent-tag {
            background-color: #FFD700;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }

        .special-price {
            background-color: #8A2BE2;
            color: white;
            padding: 10px 20px;
            border-radius: 15px;
        }

        .shop-now-btn {
            background-color: #FFD700;
            color: black;
            padding: 8px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
        }

        .shop-now-btn:hover {
            background-color: #FFC700;
        }

        .book-card {
            transition: transform 0.2s;
        }

        .book-card:hover {
            transform: scale(1.05);
        }

        .online-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #DC1E2D;
            color: white;
            padding: 3px 10px;
            border-radius: 5px;
        }

        .card-product {
            transition: transform 0.3s ease;
        }

        .card-product:hover {
            transform: scale(1.05);
        }

        .welcome-section {
            background-color: #608dee;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .carousel-item img {
            height: 400px;
            object-fit: fill;
        }

        .promo-poster {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #0b7adb, #72b7ef);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            overflow: hidden;
            position: relative;
        }

        .promo-content {
            width: 50%;
            padding: 0 50px;
            z-index: 2;
        }

        .promo-content h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .promo-content p {
            font-size: 1.2rem;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .promo-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: white;
            color: black;
            text-decoration: none;
            border-radius: 50px;
            transition: transform 0.3s ease;
        }

        .promo-button:hover {
            transform: scale(1.05);
        }

        .promo-image {
            width: 50%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .promo-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            transition: transform 0.5s ease;
        }

        .promo-image:hover img {
            transform: scale(1.1);
        }

        /* Efek partikel */
        .promo-poster::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background-image:
                radial-gradient(rgba(255, 255, 255, 0.2) 15%, transparent 16%),
                radial-gradient(rgba(255, 255, 255, 0.2) 15%, transparent 16%);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            animation: moveBackground 20s linear infinite;
            z-index: 1;
        }

        @keyframes moveBackground {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsif */
        @media (max-width: 768px) {
            .promo-poster {
                flex-direction: column;
                height: auto;
            }

            .promo-content,
            .promo-image {
                width: 100%;
                padding: 20px;
            }

            .promo-image {
                height: 250px;
            }
        }


        .content-section {
            padding: 60px 20px;
        }

        .team-member img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }

        footer {
            color: white;
        }
    </style>

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Dashboard</a>
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
                    <?php echo htmlspecialchars($user['nama_lengkap']); ?>
                </h1>
                <p style="color: white">Berikut adalah produk terbaru kami</p>
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
    <footer class="bg-dark py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h3>Information</h3>
                    <ul class="list-unstyled">
                        <li>
                            <a class="link-footer" href="about_us.html"
                                style="color: white; text-decoration: none;">About Us</a>
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