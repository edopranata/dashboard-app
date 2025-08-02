<?php

return [
    // Authentication  
    'auth' => [
        'failed' => 'Kredensial ini tidak cocok dengan catatan kami.',
        'password' => 'Kata sandi yang diberikan salah.',
        'throttle' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam :seconds detik.',
        'login_success' => 'Login berhasil',
        'logout_success' => 'Logout berhasil',
        'unauthorized' => 'Anda tidak memiliki akses ke sumber daya ini',
        'token_invalid' => 'Token tidak valid',
        'token_expired' => 'Token telah kedaluwarsa',
        'token_blacklisted' => 'Token telah diblokir',
        'profile_updated' => 'Profil berhasil diperbarui',
        'password_changed' => 'Kata sandi berhasil diubah',
        'old_password_incorrect' => 'Kata sandi saat ini salah',
    ],

    // Users
    'users' => [
        'created' => 'Pengguna berhasil dibuat',
        'updated' => 'Pengguna berhasil diperbarui',
        'deleted' => 'Pengguna berhasil dihapus',
        'not_found' => 'Pengguna tidak ditemukan',
        'cannot_delete_self' => 'Anda tidak dapat menghapus akun sendiri',
        'cannot_delete_super_admin' => 'Super Admin tidak dapat dihapus',
        'email_already_exists' => 'Alamat email sudah digunakan',
        'retrieved' => 'Data pengguna berhasil diambil',
        'user_retrieved' => 'Data pengguna berhasil diambil',
    ],

    // Roles
    'roles' => [
        'created' => 'Peran berhasil dibuat',
        'updated' => 'Peran berhasil diperbarui',
        'deleted' => 'Peran berhasil dihapus',
        'not_found' => 'Peran tidak ditemukan',
        'cannot_delete_assigned' => 'Tidak dapat menghapus peran yang sedang digunakan pengguna',
        'cannot_delete_system_role' => 'Peran sistem tidak dapat dihapus',
        'name_already_exists' => 'Nama peran sudah digunakan',
        'retrieved' => 'Data peran berhasil diambil',
        'role_retrieved' => 'Data peran berhasil diambil',
        'permissions_updated' => 'Izin peran berhasil diperbarui',
    ],

    // Permissions
    'permissions' => [
        'retrieved' => 'Data izin berhasil diambil',
        'grouped_retrieved' => 'Data izin berhasil diambil berdasarkan grup',
        'not_found' => 'Izin tidak ditemukan',
        'invalid_permission' => 'Izin yang diberikan tidak valid',
    ],

    // Dashboard
    'dashboard' => [
        'stats_retrieved' => 'Statistik dashboard berhasil diambil',
        'stats_error' => 'Gagal mengambil statistik dashboard',
    ],

    // Validation
    'validation' => [
        'required' => 'Kolom :attribute wajib diisi',
        'email' => ':attribute harus berupa alamat email yang valid',
        'unique' => ':attribute sudah digunakan',
        'min' => ':attribute minimal harus :min karakter',
        'max' => ':attribute tidak boleh lebih dari :max karakter',
        'confirmed' => 'Konfirmasi :attribute tidak cocok',
        'same' => ':attribute dan :other harus sama',
        'exists' => ':attribute yang dipilih tidak valid',
        'array' => ':attribute harus berupa array',
        'string' => ':attribute harus berupa string',
        'numeric' => ':attribute harus berupa angka',
        'boolean' => 'Kolom :attribute harus bernilai benar atau salah',
    ],

    // General
    'general' => [
        'success' => 'Operasi berhasil diselesaikan',
        'error' => 'Terjadi kesalahan',
        'not_found' => 'Sumber daya tidak ditemukan',
        'forbidden' => 'Anda tidak memiliki izin untuk melakukan tindakan ini',
        'server_error' => 'Kesalahan server internal',
        'validation_failed' => 'Validasi gagal',
        'created' => 'Sumber daya berhasil dibuat',
        'updated' => 'Sumber daya berhasil diperbarui',
        'deleted' => 'Sumber daya berhasil dihapus',
        'retrieved' => 'Sumber daya berhasil diambil',
    ],

    // System
    'system' => [
        'maintenance_mode' => 'Sistem sedang dalam pemeliharaan',
        'feature_disabled' => 'Fitur ini saat ini dinonaktifkan',
        'rate_limit_exceeded' => 'Batas laju terlampaui. Silakan coba lagi nanti',
        'api_endpoint_not_found' => 'Endpoint API tidak ditemukan',
    ],
];
