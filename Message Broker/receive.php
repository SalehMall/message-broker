<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

function konsumsiPesanDariAntrian($namaAntrian) {
    $koneksi = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    $channel = $koneksi->channel();

    // Mendeklarasikan atau membuat antrian
    $channel->queue_declare($namaAntrian, false, false, false, false);

    echo " [*] Menunggu pesan. Untuk keluar, tekan CTRL+C\n";

    $callback = function ($msg) use ($channel) {
        echo ' [x] Menerima ', $msg->body, "\n";

        // Simulasi waktu pemrosesan (dalam detik)
        $waktuPemrosesan = readline("Masukkan waktu simulasi pemrosesan pesan (detik): ");
        sleep($waktuPemrosesan);

        echo " [x] Selesai\n";
        $channel->basic_ack($msg->delivery_info['delivery_tag']);
    };

    $channel->basic_qos(null, 1, null);
    $channel->basic_consume($namaAntrian, '', false, false, false, false, $callback);

    while (count($channel->callbacks)) {
        $channel->wait();
    }

    $channel->close();
    $koneksi->close();
}

// Mendapatkan input pengguna untuk nama antrian
echo "Masukkan nama antrian : ";
$namaAntrian = readline();

// Mengkonsumsi pesan dari antrian yang ditentukan
konsumsiPesanDariAntrian($namaAntrian);
?>
