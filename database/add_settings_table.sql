USE karim_telecom;

CREATE TABLE IF NOT EXISTS parametres (
    id INT PRIMARY KEY DEFAULT 1,
    logo VARCHAR(255) DEFAULT '',
    favicon VARCHAR(255) DEFAULT '',
    nom_site VARCHAR(100) DEFAULT 'Karim Telecom',
    slogan VARCHAR(255) DEFAULT 'Téléphones & Accessoires'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO parametres (id) VALUES (1) ON DUPLICATE KEY UPDATE id=1;
