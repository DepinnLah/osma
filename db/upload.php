<?php
// Izinkan akses API (Hindari error CORS)
header('Content-Type: application/json');

// Tentukan direktori tempat penyimpanan gambar
$uploadDir = 'foto/';

// Buat foldernya jika belum ada
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Mengecek apakah request ini adalah metode POST dan ada file 'image' yang dilampirkan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    
    $file = $_FILES['image'];
    $error = $file['error'];
    
    // Pastikan tidak ada masalah dengan file yang diunggah
    if ($error === UPLOAD_ERR_OK) {
        $tmpName = $file['tmp_name'];
        $name = basename($file['name']);
        
        // Membersihkan nama file agar aman untuk server (hilangkan spasi/simbol aneh)
        $name = preg_replace("/[^a-zA-Z0-9.]/", "-", $name);
        
        // Tambahkan stempel waktu untuk memastikan nama filenya unik dan tidak bentrok
        $uniqueName = time() . '_' . $name;
        
        // Lokasi tujuan akhir file
        $destination = $uploadDir . $uniqueName;

        // Proses pemindahan dari folder penampungan sementara PHP ke direktori 'foto/' kita
        if (move_uploaded_file($tmpName, $destination)) {
            // Berhasil
            echo json_encode([
                'success' => true,
                'url' => $destination, // Mengembalikan ke Javascript: "foto/169123_gambar.jpg"
                'message' => 'Upload berhasil'
            ]);
        } else {
            // Gagal memindahkan
            echo json_encode(['success' => false, 'message' => 'Gagal memindahkan file di server.']);
        }
    } else {
        // Gagal unggah dari browser ke server
        echo json_encode(['success' => false, 'message' => 'Error upload kode: ' . $error]);
    }
} else {
    // Metode akses tidak valid (Misal dibuka via URL biasa)
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid. Pastikan unggah via form.']);
}
?>