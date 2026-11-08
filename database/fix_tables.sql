-- =============================================
-- FIX : Supprimer et recréer toutes les tables
-- Exécuter dans phpMyAdmin > onglet SQL
-- =============================================

USE karim_telecom;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS produit_villes;
DROP TABLE IF EXISTS produits;
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS villes;

SET FOREIGN_KEY_CHECKS = 1;

-- TABLE : produits
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    marque VARCHAR(100) NOT NULL,
    prix INT NOT NULL,
    categorie ENUM('telephone','accessoire') NOT NULL DEFAULT 'telephone',
    stockage VARCHAR(50) DEFAULT NULL,
    couleur VARCHAR(100) DEFAULT 'Noir',
    etat VARCHAR(50) DEFAULT 'Neuf',
    garantie VARCHAR(100) DEFAULT '1 an constructeur',
    disponibilite VARCHAR(100) DEFAULT 'Douala',
    en_stock TINYINT(1) DEFAULT 1,
    image VARCHAR(255) DEFAULT '',
    description TEXT,
    vedette TINYINT(1) DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : avis
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    note INT NOT NULL DEFAULT 5,
    texte TEXT NOT NULL,
    produit_achete VARCHAR(255) DEFAULT '',
    date_avis DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : articles
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    extrait TEXT,
    contenu LONGTEXT,
    categorie VARCHAR(100) DEFAULT 'Guide',
    temps_lecture INT DEFAULT 5,
    date_publication DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : villes
CREATE TABLE villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    delai_livraison VARCHAR(100) DEFAULT 'Livraison en 2-3 jours',
    emoji VARCHAR(10) DEFAULT '📍'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE : produit_villes
CREATE TABLE produit_villes (
    produit_id INT NOT NULL,
    ville_slug VARCHAR(100) NOT NULL,
    PRIMARY KEY (produit_id, ville_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
