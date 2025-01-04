<?php
session_start();
include '../config/database.php';

 
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

 
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];

 
$query = "SELECT * FROM produk WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result->fetch_assoc();

 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     
    $nama_produk = htmlspecialchars($_POST['nama_produk']);
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $kategori = htmlspecialchars($_POST['kategori']);

     
    $gambar = $produk['gambar'];

     
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/";
        
         
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

         
        $file_name = basename($_FILES['gambar']['name']);
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_size = $_FILES['gambar']['size'];
        
         
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($file_tmp);

         
        if (!in_array($file_type, $allowed_types)) {
            echo "<script>
                    alert('Format gambar tidak valid. Hanya JPG, PNG, dan GIF yang diperbolehkan.');
                    window.history.back();
                  </script>";
            exit;
        }

       
        if ($file_size > 2 * 1024 * 1024) {
            echo "<script>
                    alert('Ukuran file maksimal 2MB');
                    window.history.back();
                  </script>";
            exit;
        }

         
        $unique_name = uniqid() . "_" . $file_name;
        $upload_path = $target_dir . $unique_name;
 
        if (move_uploaded_file($file_tmp, $upload_path)) {
             
            if (!empty($produk['gambar']) && file_exists($produk['gambar'])) {
                unlink($produk['gambar']);
            }
            
             
            $gambar = $upload_path;
        } else {
            echo "<script>
                    alert('Gagal upload gambar');
                    window.history.back();
                  </script>";
            exit;
        }
    }

    
    $query = "UPDATE produk SET 
                nama_produk = ?, 
                harga = ?, 
                stok = ?, 
                kategori = ?, 
                gambar = ? 
              WHERE id = ?";
    
     
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdissi", 
        $nama_produk, 
        $harga, 
        $stok, 
        $kategori, 
        $gambar, 
        $id
    );

     
    if ($stmt->execute()) {
        echo "<script>
                alert('Produk berhasil diupdate');
                window.location.href='dashboard.php';
                const closeTabs = window.open('', '_self');
            closeTabs.close();
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Gagal update produk: " . $stmt->error . "');
                window.history.back();
              </script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function submitEditProduct(event) {
            event.preventDefault();
            const confirmed = confirm("Apakah Anda yakin untuk melakukan submit?");
            if(confirmed) {
                document.getElementById("form-update").submit();
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Produk</h1>
        <form method="POST" enctype="multipart/form-data" id="form-update">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" name="nama_produk" id="nama_produk" class="form-control" 
                       value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" name="harga" id="harga" class="form-control" 
                       value="<?php echo $produk['harga']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" name="stok" id="stok" class="form-control" 
                       value="<?php echo $produk['stok']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <input type="text" name="kategori" id="kategori" class="form-control" 
                       value="<?php echo htmlspecialchars($produk['kategori']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Produk</label>
                <input type="file" name="gambar" id="gambar" class="form-control">
                <p class="mt-2">Gambar Saat Ini: 
                    <img src="./uploads/<?php echo htmlspecialchars(basename($produk['gambar'])); ?>"
                         alt="Gambar Produk" style="max-width: 100px;">
                </p>
            </div>
            <button type="submit" class="btn btn-primary" onclick="submitEditProduct(event)">Simpan Perubahan</button>
            <a href="dashboard.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
