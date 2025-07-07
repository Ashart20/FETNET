<?php
return [
    // Path harus menggunakan forward slash '/' dan tanpa .exe
    'executable_path' => env('FET_EXECUTABLE_PATH', '/home/ashart20/fet-engine/fet-cl'),

    // Tambahkan baris di bawah ini
    'timeout' => 300, // Waktu tunggu dalam detik untuk proses FET
    'language' => 'en', // Bahasa output dari FET-CL
];
