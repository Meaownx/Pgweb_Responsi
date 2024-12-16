<?php
// Create connection
$conn = new mysqli("localhost", "root", "", "responsi");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet - Persebaran SPBU</title>
    <!--link leaflet dan bootstrap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style> 
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            width: 100%;
            height: calc(100vh - 56px);
        }

        /* CSS popup */
        .leaflet-popup-content-wrapper {
            max-width: 300px;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* hide tanda panah */
        .leaflet-popup-tip {
            display: none;
        }

        .navbar {
            border-bottom:#007bff;
        }

        .nav-item {
            font-size: 1rem;
        }

        /* Navbar dengan ikon */
        .navbar-nav .nav-link {
            color: #007bff !important;
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-link i {
            margin-right: 8px;
        }

        /* Styling untuk legenda */
        .leaflet-control-layers .leaflet-control-layers-base {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .legend {
            background-color: white;
            padding: 6px 8px;
            font-size: 12px;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .legend i {
            width: 18px;
            height: 18px;
            display: inline-block;
            margin-right: 5px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light" style="background-color:rgb(164, 184, 205);">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Persebaran SPBU</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="add.php"><i class="fas fa-map-marker-alt"></i> Add Data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="edit.php"><i class="fas fa-map-marker-alt"></i> Filter Fasilitas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <div id="map"></div>

    <!-- Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Detail SPBU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-wVhKuVtwbr0p6Jxz1pDBtWq++QBHYOEGLcCsF08UG8S+S2R4QudhoWBAqC7pGS8a"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="lib/L.Geoserver.js"></script>

    <script>
        // Inisialisasi peta
        var map = L.map("map").setView([-7.7337857, 110.2673721], 12);

        // Tile Layer Base Map
        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        });

        var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });

        var rupabumiindonesia = L.tileLayer('https://geoservices.big.go.id/rbi/rest/services/BASEMAP/Rupabumi_Indonesia/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Badan Informasi Geospasial'
        });

        // Menambahkan basemap ke dalam peta
        Esri_WorldImagery.addTo(map);

        // Layer Group untuk Marker dari Database
        var markerGroup = L.layerGroup().addTo(map);

        var customIcon = L.icon({
            iconUrl: './image/icon_spbu.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32],
        });

        <?php
        // Query to fetch SPBU data from database
        $sql = "SELECT * FROM spbu";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // Loop through data and add JavaScript code for each marker
            while ($row = $result->fetch_assoc()) {
                $longitude = $row['longitude'];
                $latitude = $row['latitude'];
                $info = $row['nama_spbu'];
                $address = $row['alamat'];

                // Correctly inject data into JavaScript
                echo "L.marker([$latitude, $longitude], {icon: customIcon}).addTo(markerGroup).on('click', function () {
                    var modalContent = '<strong>Nama SPBU:</strong> $info<br><strong>Alamat:</strong> $address';
                    
                    // Menggunakan Leaflet Popup untuk mengatur posisi popup
                    var popup = L.popup()
                        .setLatLng([$latitude, $longitude]) // Atur posisi di sekitar marker
                        .setContent(modalContent)
                        .openOn(map); // Menampilkan popup yang melayang
                });\n";
            }
        }
        $conn->close();
        ?>

        // WMS layers
        var wmsLayer1 = L.Geoserver.wms("https://geoportal.slemankab.go.id/geoserver/wms", {
            layers: "geonode:jalan_kabupaten_sleman_2023",
            transparent: true,
            zIndex: 50,
        });

        var wmsLayer2 = L.Geoserver.wms("http://localhost:8080/geoserver/wms", {
            layers: "pg_web_2:Sleman",
            transparent: true,
            zIndex: 20,
        });

        // Menambahkan WMS layer ke dalam peta
        wmsLayer1.addTo(map);
        wmsLayer2.addTo(map);


        // Control Layer
        var baseMaps = {
            "OpenStreetMap": osm,
            "Esri World Imagery": Esri_WorldImagery,
            "Rupa Bumi Indonesia": rupabumiindonesia,
        };

        var overlayMaps = {
            "Marker": markerGroup,
            "Jalan": wmsLayer1,
            "Kabupaten Sleman": wmsLayer2,
        };

        // Menambahkan Layer Control ke dalam peta
        L.control.layers(baseMaps, overlayMaps, { collapsed: false }).addTo(map);

        // Scale
        var scale = L.control.scale({
            position: "bottomright",
            imperial: false,
        });
        scale.addTo(map);
    </script>
</body>

</html>
