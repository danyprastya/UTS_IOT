<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MQTT Data Display</title>
    <meta http-equiv="refresh" content="5">
    <style>
        /* Mengatur tata letak agar berada di tengah */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
        }

        /* Kotak konten utama */
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        h1, h2 {
            color: #333;
            font-size: 1.5em;
            margin: 10px 0;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        p {
            font-size: 1em;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Pengaturan koneksi database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mqtt_database";

    // Membuat koneksi ke database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi ke database gagal: " . $conn->connect_error);
    }

    // URL API Python Flask
    $api_url = "http://localhost:5000/mqtt_data";

    // Mengambil data dari API JSON
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    // Menyimpan data ke dalam database
    if ($data) {
        $timestamp = date("Y-m-d H:i:s");
        $temperature = $data['temperature'] ?? null;
        $humidity = $data['humidity'] ?? null;

        // Query untuk menyimpan data ke dalam tabel "mqtt_data_history"
        $sql = "INSERT INTO mqtt_data_history (timestamp, temperature, humidity) VALUES ('$timestamp', '$temperature', '$humidity')";

        if ($conn->query($sql) === TRUE) {
            echo "<p>Data berhasil disimpan ke database.</p>";
        } else {
            echo "<p>Error menyimpan data: " . $conn->error . "</p>";
        }

        // Menampilkan data dalam bentuk tabel
        echo "<h1>Data MQTT Terbaru</h1>";
        echo "<table>";
        echo "<tr><th>Timestamp</th><th>Temperature</th><th>Humidity</th></tr>";
        echo "<tr><td>$timestamp</td><td>$temperature</td><td>$humidity</td></tr>";
        echo "</table>";

        // Menampilkan histori data
        $result = $conn->query("SELECT * FROM mqtt_data_history ORDER BY timestamp DESC");
        if ($result->num_rows > 0) {
            echo "<h2>Histori Data</h2>";
            echo "<table>";
            echo "<tr><th>Timestamp</th><th>Temperature</th><th>Humidity</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['timestamp']}</td><td>{$row['temperature']}</td><td>{$row['humidity']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Tidak ada histori data tersedia.</p>";
        }
    } else {
        echo "<p>No data available</p>";
    }

    // Menutup koneksi
    $conn->close();
    ?>
</div>

</body>
</html>
