<?php

/*
 * TEMPAT KARTU keterangan tiap negara tujuan, dalam satuan viewBox peta
 * (0 0 900 440.70631074413296) — titik PUSAT kartunya, bukan sudutnya.
 *
 * Kartu tidak bisa sekadar muncul di atas penandanya. Negara tujuan kami
 * tidak tersebar merata: empat berdesakan di Eropa Barat dan dua lagi
 * berjarak dua ratus kilometer di Asia Timur, jadi kartu yang muncul di
 * atas Belanda menimbun Jerman, Belgia, dan Inggris sekaligus — justru
 * negara-negara yang sedang ingin dilihat.
 *
 * Angkanya dicari, bukan ditaruh dengan mata: untuk tiap negara dicoba
 * titik-titik di sekelilingnya mulai dari jarak terdekat, dan yang diambil
 * yang pertama menghasilkan kotak kartu 128x50 unit yang SELURUHNYA laut
 * dan seluruhnya masih di dalam bidang peta. Arah atas dan menyamping
 * dicoba lebih dulu, arah bawah terakhir.
 *
 * Jaraknya berakhir di antara 60 dan 225 unit — Jepang paling dekat karena
 * Pasifik langsung ada di sebelahnya, Eropa paling jauh karena kartunya
 * harus menyeberang ke Atlantik Utara sebelum menemukan laut yang cukup
 * lapang.
 *
 * Kalau negara tujuan bertambah lewat panel dan kodenya belum ada di sini,
 * kartunya tidak digambar sama sekali — penandanya tetap bekerja dan
 * negaranya tetap menyala. Menaruh kartu di tempat karangan lebih buruk
 * daripada tidak menaruhnya: ia akan berdiri di atas negara lain.
 */

return [
    'AE' => ['x' => 611.2, 'y' => 318.9],
    'AU' => ['x' => 703.0, 'y' => 402.6],
    'CA' => ['x' => 82.3, 'y' => 252.9],
    'DE' => ['x' => 389.0, 'y' => 361.5],
    'IT' => ['x' => 312.4, 'y' => 208.2],
    'JP' => ['x' => 809.7, 'y' => 232.5],
    'KR' => ['x' => 810.5, 'y' => 229.3],
    'NL' => ['x' => 313.0, 'y' => 210.5],
    'SG' => ['x' => 631.4, 'y' => 331.0],
    'US' => ['x' => 121.6, 'y' => 270.6],
];
