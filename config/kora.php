<?php

return [
    // ID baris users untuk akun KORA (buat lewat menu User). Dipakai sebagai penginput kreator,
    // PIC dan approve pada baris yang dibuat KORA, sekaligus penanda "dikerjakan KORA".
    'user_id' => env('KORA_USER_ID') ? (int) env('KORA_USER_ID') : null,

    // Token bearer untuk /kora-api/*. Kosong = API KORA mati (404).
    'api_token' => env('KORA_API_TOKEN'),
];
