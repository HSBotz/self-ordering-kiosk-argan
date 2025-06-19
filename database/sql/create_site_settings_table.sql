-- Buat tabel site_settings
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(255) DEFAULT 'general',
  `type` varchar(255) DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Masukkan data default untuk footer
INSERT INTO `site_settings` (`key`, `value`, `group`, `type`, `description`, `created_at`, `updated_at`) VALUES
('footer_about_title', 'Kedai Coffee Kiosk', 'footer', 'text', 'Judul kolom tentang di footer', NOW(), NOW()),
('footer_about_text', 'Nikmati kopi premium kami dengan layanan self-ordering yang mudah dan cepat.', 'footer', 'textarea', 'Teks kolom tentang di footer', NOW(), NOW()),
('footer_social_facebook', '#', 'footer', 'text', 'URL Facebook', NOW(), NOW()),
('footer_social_instagram', '#', 'footer', 'text', 'URL Instagram', NOW(), NOW()),
('footer_social_twitter', '#', 'footer', 'text', 'URL Twitter', NOW(), NOW()),
('footer_hours_title', 'Jam Buka', 'footer', 'text', 'Judul kolom jam buka di footer', NOW(), NOW()),
('footer_hours_weekday', 'Senin - Jumat: 08:00 - 22:00', 'footer', 'text', 'Jam buka hari kerja', NOW(), NOW()),
('footer_hours_weekend', 'Sabtu - Minggu: 09:00 - 23:00', 'footer', 'text', 'Jam buka akhir pekan', NOW(), NOW()),
('footer_contact_title', 'Kontak', 'footer', 'text', 'Judul kolom kontak di footer', NOW(), NOW()),
('footer_contact_address', 'Jl. Kopi No. 123, Kota', 'footer', 'text', 'Alamat di footer', NOW(), NOW()),
('footer_contact_phone', '+62 123 4567 890', 'footer', 'text', 'Nomor telepon di footer', NOW(), NOW()),
('footer_contact_email', 'info@kedaicoffee.com', 'footer', 'text', 'Email di footer', NOW(), NOW()),
('footer_copyright', 'Kedai Coffee Kiosk. Semua hak dilindungi.', 'footer', 'text', 'Teks copyright di footer', NOW(), NOW());
