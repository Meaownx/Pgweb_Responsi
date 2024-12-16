<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "responsi");

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil id_spbu dari URL
$id_spbu = isset($_GET['id']) ? $_GET['id'] : 0;

// Jika id_spbu tidak ditemukan, redirect ke halaman lain atau beri pesan error
if ($id_spbu == 0) {
    echo "ID SPBU tidak ditemukan.";
    exit;
}

// Ambil data SPBU berdasarkan id_spbu
$sql = "SELECT spbu.id_spbu, spbu.nama_spbu, fasilitas_spbu.24jam, fasilitas_spbu.atm, fasilitas_spbu.mushola, fasilitas_spbu.toilet, fasilitas_spbu.minimarket, fasilitas_spbu.nitrogen
        FROM spbu
        JOIN fasilitas_spbu ON spbu.id_spbu = fasilitas_spbu.id_spbu
        WHERE spbu.id_spbu = $id_spbu";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Data tidak ditemukan.";
    exit;
}

// Proses form jika tombol update ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_spbu = $_POST['nama_spbu'];
    $buka24jam = isset($_POST['24jam']) ? 1 : 0;
    $atm = isset($_POST['atm']) ? 1 : 0;
    $mushola = isset($_POST['mushola']) ? 1 : 0;
    $toilet = isset($_POST['toilet']) ? 1 : 0;
    $minimarket = isset($_POST['minimarket']) ? 1 : 0;
    $nitrogen = isset($_POST['nitrogen']) ? 1 : 0;

    // Update data SPBU di database
    $update_sql = "UPDATE fasilitas_spbu 
                   SET 24jam = $buka24jam, atm = $atm, mushola = $mushola, toilet = $toilet, minimarket = $minimarket, nitrogen = $nitrogen 
                   WHERE id_spbu = $id_spbu";
    $update_spbu_sql = "UPDATE spbu SET nama_spbu = '$nama_spbu' WHERE id_spbu = $id_spbu";

    if ($conn->query($update_spbu_sql) === TRUE && $conn->query($update_sql) === TRUE) {
        // Tampilkan pesan sukses dan arahkan ke halaman sebelumnya setelah beberapa detik
        echo "<script>
        alert('Data berhasil diperbarui!');
        window.location.href = 'edit.php';
      </script>";

    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data SPBU</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="alert alert-info" role="alert">
            <h5>Update Data SPBU</h5>
        </div>

        <form action="update.php?id=<?php echo $id_spbu; ?>" method="POST">
            <div class="form-container">
                <div class="mb-3">
                    <label for="nama_spbu" class="form-label">Nama SPBU</label>
                    <input type="text" class="form-control" id="nama_spbu" name="nama_spbu"
                        value="<?php echo $row['nama_spbu']; ?>" required>
                </div>

                <div class="checkbox-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="24jam" name="24jam" value="1" <?php echo $row['24jam'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="24jam">Buka 24 Jam</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="atm" name="atm" value="1" <?php echo $row['atm'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="atm">ATM</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mushola" name="mushola" value="1" <?php echo $row['mushola'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="mushola">Mushola</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="toilet" name="toilet" value="1" <?php echo $row['toilet'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="toilet">Toilet</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="minimarket" name="minimarket" value="1"
                            <?php echo $row['minimarket'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="minimarket">Minimarket</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="nitrogen" name="nitrogen" value="1" <?php echo $row['nitrogen'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="nitrogen">Nitrogen</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-info w-100">Update</button>
            </div>
        </form>
    </div>
</body>

</html>