<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "responsi");

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Menangani form penambahan data SPBU
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nama_spbu'])) {
    $nama_spbu = $_POST['nama_spbu'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $fasilitas = isset($_POST['fasilitas']) ? $_POST['fasilitas'] : [];

    // Masukkan data SPBU ke tabel spbu
    $sql_spbu = "INSERT INTO spbu (nama_spbu, latitude, longitude) VALUES ('$nama_spbu', '$latitude', '$longitude')";
    if ($conn->query($sql_spbu) === TRUE) {
        $id_spbu = $conn->insert_id;

        // Masukkan data fasilitas SPBU
        $sql_fasilitas = "INSERT INTO fasilitas_spbu (id_spbu, 24jam, atm, mushola, toilet, minimarket, nitrogen) 
                          VALUES ('$id_spbu', 
                                  " . (in_array('24jam', $fasilitas) ? 1 : 0) . ", 
                                  " . (in_array('atm', $fasilitas) ? 1 : 0) . ", 
                                  " . (in_array('mushola', $fasilitas) ? 1 : 0) . ", 
                                  " . (in_array('toilet', $fasilitas) ? 1 : 0) . ", 
                                  " . (in_array('minimarket', $fasilitas) ? 1 : 0) . ", 
                                  " . (in_array('nitrogen', $fasilitas) ? 1 : 0) . ")";
        $conn->query($sql_fasilitas);
    }
}

// Ambil data dari checkbox untuk filter fasilitas
$fasilitas = isset($_GET['fasilitas']) ? $_GET['fasilitas'] : [];

// Query dasar dengan JOIN untuk mengambil nama SPBU dan fasilitas
$sql = "SELECT spbu.id_spbu, spbu.nama_spbu, spbu.latitude, spbu.longitude, fasilitas_spbu.24jam, fasilitas_spbu.atm, fasilitas_spbu.mushola, fasilitas_spbu.toilet, fasilitas_spbu.minimarket, fasilitas_spbu.nitrogen
        FROM spbu
        JOIN fasilitas_spbu ON spbu.id_spbu = fasilitas_spbu.id_spbu";

// Tambahkan kondisi jika ada filter
if (!empty($fasilitas)) {
    $conditions = [];
    foreach ($fasilitas as $fasilitas_item) {
        $conditions[] = "$fasilitas_item = 1";
    }
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Jalankan query
$result = $conn->query($sql);
?>

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
    
    <!-- Formulir untuk Menambah Data SPBU -->
    <div class="alert alert-info" role="alert">
        <h5>Tambah Data SPBU</h5>
    </div>
    <form action="" method="POST">
        <div class="form-container">
            <div class="row">
                <div class="col-md-6">
                    <label for="nama_spbu" class="form-label">Nama SPBU</label>
                    <input type="text" class="form-control" id="nama_spbu" name="nama_spbu" required>
                </div>
                <div class="col-md-6">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" class="form-control" id="latitude" name="latitude" required>
                </div>
                <div class="col-md-6">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" class="form-control" id="longitude" name="longitude" required>
                </div>
                <div class="col-md-6">
                    <label for="fasilitas" class="form-label">Pilih Fasilitas</label>
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="24jam_add" name="fasilitas[]" value="24jam">
                            <label class="form-check-label" for="24jam_add">Buka 24 Jam</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="atm_add" name="fasilitas[]" value="atm">
                            <label class="form-check-label" for="atm_add">ATM</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mushola_add" name="fasilitas[]" value="mushola">
                            <label class="form-check-label" for="mushola_add">Mushola</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="toilet_add" name="fasilitas[]" value="toilet">
                            <label class="form-check-label" for="toilet_add">Toilet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="minimarket_add" name="fasilitas[]" value="minimarket">
                            <label class="form-check-label" for="minimarket_add">Minimarket</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="nitrogen_add" name="fasilitas[]" value="nitrogen">
                            <label class="form-check-label" for="nitrogen_add">Nitrogen</label>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-info mt-3">Tambah Data SPBU</button>
        </div>
    </form>

    <hr>

    <!-- Tampilkan Data SPBU -->
    <div class="alert alert-info" role="alert">
        <h5>Data SPBU</h5>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama SPBU</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Fasilitas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['nama_spbu'] . "</td>";
                    echo "<td>" . $row['latitude'] . "</td>";
                    echo "<td>" . $row['longitude'] . "</td>";
                    echo "<td>";
                    $fasilitas = [];
                    if ($row['24jam']) $fasilitas[] = "Buka 24 Jam";
                    if ($row['atm']) $fasilitas[] = "ATM";
                    if ($row['mushola']) $fasilitas[] = "Mushola";
                    if ($row['toilet']) $fasilitas[] = "Toilet";
                    if ($row['minimarket']) $fasilitas[] = "Minimarket";
                    if ($row['nitrogen']) $fasilitas[] = "Nitrogen";
                    echo implode(", ", $fasilitas);
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data SPBU</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
// Tutup koneksi
$conn->close();
?>
