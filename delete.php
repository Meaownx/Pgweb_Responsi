<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "responsi");

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID yang akan dihapus
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Hapus data yang terkait di tabel fasilitas_spbu terlebih dahulu
    $delete_fasilitas_spbu_sql = "DELETE FROM fasilitas_spbu WHERE id_spbu = ?";

    if ($stmt_fasilitas_spbu = $conn->prepare($delete_fasilitas_spbu_sql)) {
        $stmt_fasilitas_spbu->bind_param('i', $delete_id);

        // Eksekusi query
        if ($stmt_fasilitas_spbu->execute()) {
            // Setelah menghapus data di fasilitas_spbu, hapus data di spbu
            $delete_spbu_sql = "DELETE FROM spbu WHERE id_spbu = ?";

            if ($stmt_spbu = $conn->prepare($delete_spbu_sql)) {
                $stmt_spbu->bind_param('i', $delete_id);

                // Eksekusi query untuk menghapus data spbu
                if ($stmt_spbu->execute()) {
                    echo "<script>alert('Record berhasil dihapus dari kedua tabel.'); window.location.href='edit.php';</script>";
                } else {
                    $error_message = $stmt_spbu->error;
                    echo "<script>alert('Error saat menghapus record di tabel spbu: $error_message'); window.location.href='edit.php';</script>";
                }
                $stmt_spbu->close();
            } else {
                $error_message = $conn->error;
                echo "<script>alert('Gagal menyiapkan pernyataan untuk spbu: $error_message'); window.location.href='edit.php';</script>";
            }
        } else {
            $error_message = $stmt_fasilitas_spbu->error;
            echo "<script>alert('Error saat menghapus record di fasilitas_spbu: $error_message'); window.location.href='edit.php';</script>";
        }
        $stmt_fasilitas_spbu->close();
    } else {
        $error_message = $conn->error;
        echo "<script>alert('Gagal menyiapkan pernyataan untuk fasilitas_spbu: $error_message'); window.location.href='edit.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid.'); window.location.href='edit.php';</script>";
}

$conn->close();
?>