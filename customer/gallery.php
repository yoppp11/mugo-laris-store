<?php
// Sambungkan database
include '../config/database.php';

// Query ambil semua produk
$query = "SELECT * FROM produk ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="gallery-style.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .product-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .product-card-img {
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-card-img {
            transform: scale(1.05);
        }

        .product-price {
            font-weight: bold;
            color: #007bff;
        }

        .product-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .filter-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .product-card-img {
                height: 200px;
            }
        }

        .navbar {
            background-color: #608dee;
            max-height: 50px;
        }

        .navbar-brand {
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
                        <a class="nav-link active" href="#">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./chart.php">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../about_us.html">Tentang Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid py-5">
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-select" id="kategoriFilter">
                                <option value="">Semua Kategori</option>
                                <?php
                                // Ambil kategori unik
                                $kategori_query = "SELECT DISTINCT kategori FROM produk";
                                $kategori_result = mysqli_query($conn, $kategori_query);
                                while ($kategori = mysqli_fetch_assoc($kategori_result)) {
                                    echo "<option value='" . $kategori['kategori'] . "'>" . $kategori['kategori'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari Produk...">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="sortFilter">
                                <option value="terbaru">Terbaru</option>
                                <option value="termurah">Termurah</option>
                                <option value="termahal">Termahal</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Section -->
        <div class="row" id="produkContainer">
            <?php while ($produk = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-3 col-sm-6 produk-item" data-kategori="<?= $produk['kategori'] ?>"
                    data-harga="<?= $produk['harga'] ?>">
                    <div class="card product-card">
                        <img src="../admin/uploads/<?php echo htmlspecialchars(basename($produk['gambar'])); ?>"
                            class="card-img-top product-card-img" alt="<?= $produk['nama_produk'] ?>">

                        <div class="card-body">
                            <h5 class="card-title product-title"><?= $produk['nama_produk'] ?></h5>
                            <p class="card-text product-price">Rp. <?= number_format($produk['harga'], 0, ',', '.') ?></p>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary"><?= $produk['kategori'] ?></span>
                                <a href="detail_produk.php?id=<?= $produk['id'] ?>" class="btn btn-sm btn-custom">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript untuk Filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kategoriFilter = document.getElementById('kategoriFilter');
            const searchInput = document.getElementById('searchInput');
            const sortFilter = document.getElementById('sortFilter');
            const produkContainer = document.getElementById('produkContainer');
            const produkItems = document.querySelectorAll('.produk-item');

            function filterProduk() {
                const kategori = kategoriFilter.value;
                const search = searchInput.value.toLowerCase();

                produkItems.forEach(item => {
                    const itemKategori = item.getAttribute('data-kategori');
                    const itemNama = item.querySelector('.card-title').textContent.toLowerCase();

                    const kategoriMatch = kategori === '' || itemKategori === kategori;
                    const searchMatch = itemNama.includes(search);

                    if (kategoriMatch && searchMatch) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            kategoriFilter.addEventListener('change', filterProduk);
            searchInput.addEventListener('input', filterProduk);
        });
    </script>
</body>

</html>