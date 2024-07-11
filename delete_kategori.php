<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['peran'] != 1) {
    header("Location: index.php");
    exit();
}

require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM kategori_agenda WHERE id_kategori = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: kategori_agenda.php");
exit();
?>
