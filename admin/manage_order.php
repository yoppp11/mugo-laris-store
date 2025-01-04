<?php
session_start();
include '../config/database.php';

 
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

 
$query = "SELECT * FROM pesanan ORDER BY created_at DESC";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $status_pesanan = $_POST['status_pesanan'];

    $update_query = "UPDATE pesanan SET status_pesanan = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status_pesanan, $pesanan_id);
    if ($stmt->execute()) {
        $success_message = "Status pesanan berhasil diperbarui.";
    } else {
        $error_message = "Gagal memperbarui status pesanan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Kelola Pesanan</h1>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"> <?php echo $success_message; ?> </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
        <?php endif; ?>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>User ID</th>
                    <th>Total Harga</th>
                    <th>Status Pesanan</th>
                    <th>Tanggal Pesanan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($pesanan = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $pesanan['id']; ?></td>
                        <td><?php echo $pesanan['user_id']; ?></td>
                        <td>Rp<?php echo number_format($pesanan['total_harga'], 2, ',', '.'); ?></td>
                        <td><?php echo $pesanan['status_pesanan']; ?></td>
                        <td><?php echo $pesanan['created_at']; ?></td>
                        <td>
                            <form method="POST" class="d-inline-block">
                                <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                                <select name="status_pesanan" class="form-select form-select-sm mb-2">
                                    <option value="Menunggu" <?php echo $pesanan['status_pesanan'] == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="Diproses" <?php echo $pesanan['status_pesanan'] == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                                    <option value="Dikirim" <?php echo $pesanan['status_pesanan'] == 'Dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                                    <option value="Selesai" <?php echo $pesanan['status_pesanan'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">Ubah Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
