<?php
 
// Set timezone ke Indonesia bagian Barat (WIB) supaya jam absen sesuai jam lokal
date_default_timezone_set('Asia/Jakarta');
 
$host = "localhost";
$user = "root";
$pass = "";
$database = "db_sekolah";
 
$koneksi = mysqli_connect($host, $user, $pass, $database);
 
if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
};