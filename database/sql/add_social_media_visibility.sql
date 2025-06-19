-- Tambahkan pengaturan untuk visibilitas media sosial
INSERT INTO `site_settings` (`key`, `value`, `group`, `type`, `description`, `created_at`, `updated_at`) VALUES
('footer_social_media_visible', '1', 'footer', 'boolean', 'Apakah media sosial ditampilkan di footer', NOW(), NOW());
