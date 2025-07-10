<?php
return [
    // Path harus menggunakan forward slash '/' dan tanpa .exe
    'executable_path' => env('FET_EXECUTABLE_PATH', '/home/ashart20/fet-engine/fet-cl'),

    // Tambahkan baris di bawah ini
    'timeout' => 300, // Waktu tunggu dalam detik untuk proses FET
    'language' => 'en', // Bahasa output dari FET-CL



    /**
     * Path absolut ke folder yang berisi library Qt (libQt6Core.so.6, dll).
     * Biasanya ada di dalam folder 'lib' di samping file executable.
     */
    'qt_library_path' => env('FET_QT_LIBRARY_PATH', '/home/ashart20/fet-7.2.5/usr/lib'),


];
