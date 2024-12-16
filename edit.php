<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter & Update Data SPBU</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .form-container {
            background-color: #f0f8ff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .checkbox-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .checkbox-group .form-check {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <!-- Formulir untuk Filter Data -->
    <div class="alert alert-info" role="alert">
        <h5>Filter Data SPBU</h5>
    </div>
    <form action="" method="GET">
        <div class="form-container">
            <div class="row">
                <div class="col-md-6">
                    <label for="filter_fasilitas" class="form-label">Pilih Fasilitas</label>
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="24jam" name="fasilitas[]" value="24jam">
                            <label class="form-check-label" for="24jam">Buka 24 Jam</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="atm" name="fasilitas[]" value="atm">
                            <label class="form-check-label" for="atm">ATM</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mushola" name="fasilitas[]" value="mushola">
                            <label class="form-check-label" for="mushola">Mushola</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="toilet" name="fasilitas[]" value="toilet">
                            <label class="form-check-label" for="toilet">Toilet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="minimarket" name="fasilitas[]" value="minimarket">
                            <label class="form-check-label" for="minimarket">Minimarket</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="nitrogen" name="fasilitas[]" value="nitrogen">
                            <label class="form-check-label" for="nitrogen">Nitrogen</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </div>
    </form>

    <hr>

    <!-- Tampilkan Data Hasil Filter -->
    <?php
    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "responsi");

    // Cek koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }



if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Siapkan dan eksekusi query delete
    $delete_sql = "DELETE spbu, fasilitas_spbu FROM spbu 
                   INNER JOIN fasilitas_spbu ON spbu.id_spbu = fasilitas_spbu.id_spbu
                   WHERE spbu.id_spbu = ?";
    if ($stmt = $conn->prepare($delete_sql)) {
        // Ikat parameter
        $stmt->bind_param('i', $delete_id);  // 'i' untuk integer
        
        if (!$stmt->execute()) {
            // Eksekusi gagal, dapatkan pesan kesalahan
            $error_message = $stmt->error;
            echo "<script>alert('Error saat menghapus record: $error_message'); window.location.href='edit.php';</script>";
        } else {
            echo "<script>alert('Record berhasil dihapus.'); window.location.href='edit.php';</script>";
        }
        
        $stmt->close(); 
        // Gagal menyiapkan pernyataan
        $error_message = $conn->error;
        echo "<script>alert('Gagal menyiapkan pernyataan: $error_message'); window.location.href='edit.php';</script>";
    }
}


    // Ambil data dari checkbox
    $fasilitas = isset($_GET['fasilitas']) ? $_GET['fasilitas'] : [];

    // Query dasar dengan JOIN untuk mengambil nama SPBU dan fasilitas
    $sql = "SELECT spbu.id_spbu, spbu.nama_spbu, fasilitas_spbu.24jam, fasilitas_spbu.atm, fasilitas_spbu.mushola, fasilitas_spbu.toilet, fasilitas_spbu.minimarket, fasilitas_spbu.nitrogen
    FROM spbu
    JOIN fasilitas_spbu ON spbu.id_spbu = fasilitas_spbu.id_spbu";

    // Tambahkan kondisi jika ada filter
    if (!empty($fasilitas)) {
        $conditions = [];
        foreach ($fasilitas as $fasilitas_item) {
            $conditions[] = "$fasilitas_item = 1";
        }
        // Gabungkan semua kondisi dengan AND
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Jalankan query
    $result = $conn->query($sql);

    // Tampilkan data di tabel
    if ($result->num_rows > 0) {
        echo "<table class='table table-striped table-primary table-bordered'><tr>
            <th>ID SPBU</th>
            <th>Nama SPBU</th>
            <th>Buka 24 JAM</th>
            <th>ATM</th>
            <th>Mushola</th>
            <th>Toilet</th>
            <th>Minimarket</th>
            <th>Nitrogen</th>
            <th>Aksi</th></tr>";

        // Output data untuk setiap baris
        while ($row = $result->fetch_assoc()) {
            $buka24jam = ($row["24jam"] == 1) ? "Ada" : "Tidak Ada";
            $atm = ($row["atm"] == 1) ? "Ada" : "Tidak Ada";
            $mushola = ($row["mushola"] == 1) ? "Ada" : "Tidak Ada";
            $toilet = ($row["toilet"] == 1) ? "Ada" : "Tidak Ada";
            $minimarket = ($row["minimarket"] == 1) ? "Ada" : "Tidak Ada";
            $nitrogen = ($row["nitrogen"] == 1) ? "Ada" : "Tidak Ada";

            echo "<tr>
               <td>" . $row["id_spbu"] . "</td>
                <td>" . $row["nama_spbu"] . "</td>
                <td>" . $buka24jam . "</td>
                <td>" . $atm . "</td>
                <td>" . $mushola . "</td>
                <td>" . $toilet . "</td>
                <td>" . $minimarket . "</td>
                <td>" . $nitrogen . "</td>
                <td>
                <a href='update.php?id=" . $row["id_spbu"] . "' class='btn btn-warning btn-sm'>Update</a>
                 <a href='delete.php?delete_id=" . $row["id_spbu"] . "' onclick='return confirm(\"Are you sure you want to delete this item?\");' class='btn btn-danger btn-sm'>Delete</a>
                </td>";
        }
        echo "</table>";
    } else {
        echo "<p>Tidak ada data yang sesuai dengan filter.</p>";
    }

    $conn->close();
    ?>
    
</div>
</body>
</html>
