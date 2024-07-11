<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['peran'] != 1) {
    header("Location: index.php");
    exit();
}
include 'db.php';

require 'vendor/autoload.php';

$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

// Query untuk mengambil data kehadiran
$query_confirmed = "SELECT pengguna.nama_pengguna, agenda.nama_agenda, konfirmasi_kehadiran.tanggal_konfirmasi
                    FROM konfirmasi_kehadiran
                    INNER JOIN pengguna ON konfirmasi_kehadiran.id_pengguna = pengguna.id_pengguna
                    INNER JOIN agenda ON konfirmasi_kehadiran.id_agenda = agenda.id_agenda";
$result_confirmed = mysqli_query($conn, $query_confirmed);

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="rekap_kehadiran.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Nama Pengguna', 'Agenda', 'Waktu Konfirmasi']);
    while ($row = mysqli_fetch_assoc($result_confirmed)) {
        fputcsv($output, [
            $row['nama_pengguna'],
            $row['nama_agenda'],
            $row['tanggal_konfirmasi']
        ]);
    }
    fclose($output);
    exit();

} elseif ($format === 'word') {
    header('Content-Type: application/vnd.ms-word');
    header('Content-Disposition: attachment; filename="rekap_kehadiran.doc"');
    echo '<html><head><meta charset="UTF-8"></head><body>';
    echo '<h2>Laporan Aktivitas Warga</h2>';
    echo '<table border="1" width="100%" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Agenda</th>
                    <th>Waktu Konfirmasi</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = mysqli_fetch_assoc($result_confirmed)) {
        echo '<tr>
                  <td>' . htmlspecialchars($row['nama_pengguna']) . '</td>
                  <td>' . htmlspecialchars($row['nama_agenda']) . '</td>
                  <td>' . htmlspecialchars($row['tanggal_konfirmasi']) . '</td>
              </tr>';
    }
    echo '</tbody></table></body></html>';
    exit();
}
