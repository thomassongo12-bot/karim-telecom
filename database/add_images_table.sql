USE karim_telecom;

-- Ajouter une table pour les images produit (plusieurs images par produit)
CREATE TABLE IF NOT EXISTS produit_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    is_main TINYINT(1) DEFAULT 0,
    ordre INT DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_produit (produit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
