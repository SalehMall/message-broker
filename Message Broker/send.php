<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function kirimPesanKeAntrian($data, $namaAntrian) {
    $koneksi = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    $channel = $koneksi->channel();

    // Membuat atau mendeklarasikan antrian
    $channel->queue_declare($namaAntrian, false, false, false, false);

    // Membuat pesan
    $pesan = new AMQPMessage(
        $data,
        array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
    );

    // Mengirim pesan ke antrian
    $channel->basic_publish($pesan, '', $namaAntrian);

    echo ' [x] Pesan Terkirim: ', $data, ' ke antrian ', $namaAntrian, "\n";

    // Menutup channel dan koneksi
    $channel->close();
    $koneksi->close();
}

// Mendapatkan input pengguna untuk pesan dan nama antrian
echo "Masukkan pesanan anda: ";
$data = readline();

echo "Masukkan nama Queues: ";
$namaAntrian = readline();

// Mengirim pesan ke antrian yang ditentukan
kirimPesanKeAntrian($data, $namaAntrian);
?>
