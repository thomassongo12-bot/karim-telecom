-- =============================================
-- Karim Telecom — Import Hostinger
-- NE PAS modifier CREATE DATABASE ni USE
-- Importez directement dans votre base Hostinger
-- =============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET time_zone = '+00:00';

-- ── TABLE : produits ──
CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    marque VARCHAR(100) NOT NULL,
    prix INT NOT NULL,
    categorie VARCHAR(100) NOT NULL DEFAULT 'telephone',
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

-- ── TABLE : produit_images ──
CREATE TABLE IF NOT EXISTS produit_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    is_main TINYINT(1) DEFAULT 0,
    ordre INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLE : categories ──
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    image VARCHAR(255) DEFAULT '',
    ordre INT DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLE : avis ──
CREATE TABLE IF NOT EXISTS avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    photo VARCHAR(255) DEFAULT '',
    note INT NOT NULL DEFAULT 5,
    texte TEXT NOT NULL,
    produit_achete VARCHAR(255) DEFAULT '',
    date_avis DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLE : articles ──
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    extrait TEXT,
    contenu LONGTEXT,
    categorie VARCHAR(100) DEFAULT 'Guide achat',
    image VARCHAR(255) DEFAULT '',
    temps_lecture INT DEFAULT 5,
    date_publication DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLE : villes ──
CREATE TABLE IF NOT EXISTS villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    delai_livraison VARCHAR(100) DEFAULT 'Livraison en 2-3 jours',
    emoji VARCHAR(10) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLE : produit_villes ──
CREATE TABLE IF NOT EXISTS produit_villes (
    produit_id INT NOT NULL,
    ville_slug VARCHAR(100) NOT NULL,
    PRIMARY KEY (produit_id, ville_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLE : parametres ──
CREATE TABLE IF NOT EXISTS parametres (
    id INT PRIMARY KEY DEFAULT 1,
    logo VARCHAR(255) DEFAULT '',
    favicon VARCHAR(255) DEFAULT '',
    nom_site VARCHAR(100) DEFAULT 'Karim Telecom',
    slogan VARCHAR(255) DEFAULT 'Telephones & Accessoires'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO parametres (id) VALUES (1) ON DUPLICATE KEY UPDATE id=1;

-- ── TABLE : site_visits ──
CREATE TABLE IF NOT EXISTS site_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(255) NOT NULL,
    ip_hash VARCHAR(64) NOT NULL,
    user_agent VARCHAR(255) DEFAULT '',
    referrer VARCHAR(255) DEFAULT '',
    date_visite DATE NOT NULL,
    heure TINYINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLE : produit_clics ──
CREATE TABLE IF NOT EXISTS produit_clics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    ip_hash VARCHAR(64) NOT NULL,
    date_clic DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNEES
-- ============================================

-- Produits
INSERT IGNORE INTO produits (nom, slug, marque, prix, categorie, stockage, couleur, etat, garantie, disponibilite, en_stock, image, description, vedette) VALUES
('iPhone 17 Pro Max 256GB', 'iphone-17-pro-max', 'Apple', 1250000, 'apple', '256 Go', 'Titane Naturel', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'L\'iPhone 17 Pro Max redefunit les standards du smartphone. Puce A19 Pro, ecran 6,9 pouces OLED, camera ProRes 8K.', 1),
('iPhone 17 Pro 256GB', 'iphone-17-pro', 'Apple', 1050000, 'apple', '256 Go', 'Titane Blanc', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'L\'iPhone 17 Pro en format compact 6,3 pouces. Puce A19 Pro, Dynamic Island, camera 48MP.', 1),
('iPhone 16 Pro Max 256GB', 'iphone-16-pro-max', 'Apple', 950000, 'apple', '256 Go', 'Titane Desert', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'L\'iPhone 16 Pro Max avec Apple Intelligence. Ecran ProMotion 120Hz 6,9 pouces, puce A18 Pro.', 1),
('Samsung Galaxy S25 Ultra', 'samsung-galaxy-s25-ultra', 'Samsung', 1150000, 'samsung', '256 Go', 'Titanium Black', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Galaxy S25 Ultra avec S Pen integre. Snapdragon 8 Elite, camera 200MP, zoom 10x, Galaxy AI.', 1),
('Samsung Galaxy A56', 'samsung-galaxy-a56', 'Samsung', 320000, 'samsung', '128 Go', 'Awesome Lilac', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Samsung Galaxy A56 : ecran AMOLED 120Hz, triple camera 50MP, IP67.', 1),
('Google Pixel 9', 'google-pixel-9', 'Google', 680000, 'google', '128 Go', 'Obsidian', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Google Pixel 9 avec Gemini AI integre. Les meilleures photos computationnelles du marche.', 0),
('iPhone 15 128GB', 'iphone-15', 'Apple', 720000, 'apple', '128 Go', 'Noir', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'L\'iPhone 15 avec Dynamic Island et USB-C. Puce A16 Bionic, camera 48MP.', 1),
('Samsung Galaxy S24', 'samsung-galaxy-s24', 'Samsung', 780000, 'samsung', '256 Go', 'Onyx Black', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Galaxy S24 avec Galaxy AI. Snapdragon 8 Gen 3, ecran AMOLED 6,2 pouces 120Hz.', 0),
('Xiaomi 14', 'xiaomi-14', 'Xiaomi', 450000, 'xiaomi', '256 Go', 'Noir', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Xiaomi 14 avec triple camera Leica et Snapdragon 8 Gen 3. Charge ultra-rapide 90W.', 0),
('Tecno Spark 20', 'tecno-spark-20', 'Tecno', 95000, 'tecno', '128 Go', 'Magic Skin White', 'Neuf', '6 mois', 'Douala', 1, '', 'Le Tecno Spark 20 : grand ecran 6,56 pouces, batterie 5000 mAh, camera 50MP.', 1),
('Infinix Hot 40', 'infinix-hot-40', 'Infinix', 85000, 'infinix', '128 Go', 'Starfall Green', 'Neuf', '6 mois', 'Douala', 1, '', 'L\'Infinix Hot 40 : ecran 6,78 pouces, batterie 5000 mAh, double SIM.', 1),
('AirPods Pro 2', 'airpods-pro-2', 'Apple', 180000, 'accessoire', NULL, 'Blanc', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Les AirPods Pro 2 avec reduction de bruit active H2. Son spatial, 30h d\'autonomie.', 1);

-- Categories
INSERT IGNORE INTO categories (nom, slug, image, ordre) VALUES
('Apple',       'apple',      '', 1),
('Samsung',     'samsung',    '', 2),
('Xiaomi',      'xiaomi',     '', 3),
('Google',      'google',     '', 4),
('Tecno',       'tecno',      '', 5),
('Infinix',     'infinix',    '', 6),
('Accessoires', 'accessoire', '', 7);

-- Villes
INSERT IGNORE INTO villes (nom, slug, description, delai_livraison) VALUES
('Douala',    'douala',    'Karim Telecom est base a Douala. Livraison le jour meme dans tous les quartiers.',         'Livraison le jour meme (J+0)'),
('Yaounde',   'yaounde',   'Nous livrons a Yaounde avec des partenaires fiables.',                                     'Livraison en 24-48h'),
('Bafoussam', 'bafoussam', 'Karim Telecom dessert Bafoussam et toute la region de l\'Ouest.',                         'Livraison en 2-3 jours'),
('Bamenda',   'bamenda',   'Nous livrons dans la region du Nord-Ouest, a Bamenda et environs.',                       'Livraison en 2-3 jours'),
('Garoua',    'garoua',    'Karim Telecom livre dans le Nord du Cameroun, a Garoua.',                                 'Livraison en 3-4 jours');

-- Produit-Villes
INSERT IGNORE INTO produit_villes (produit_id, ville_slug) VALUES
(1,'douala'),(1,'yaounde'),(2,'douala'),(2,'yaounde'),(2,'bafoussam'),
(3,'douala'),(3,'yaounde'),(3,'bafoussam'),(4,'douala'),(4,'yaounde'),
(5,'douala'),(5,'yaounde'),(5,'bafoussam'),(5,'bamenda'),(5,'garoua'),
(6,'douala'),(6,'yaounde'),(7,'douala'),(7,'yaounde'),(7,'bafoussam'),
(8,'douala'),(8,'yaounde'),(8,'bafoussam'),(9,'douala'),(9,'yaounde'),
(10,'douala'),(10,'yaounde'),(10,'bafoussam'),(10,'bamenda'),(10,'garoua'),
(11,'douala'),(11,'yaounde'),(11,'bafoussam'),(11,'bamenda'),(11,'garoua'),
(12,'douala'),(12,'yaounde');

-- Avis clients
INSERT IGNORE INTO avis (nom, ville, note, texte, produit_achete, date_avis) VALUES
('Armand Tchamba',    'Douala',    5, 'Excellent service ! iPhone commande le matin, recu l apres-midi. Telephone 100% original. Je recommande !',              'iPhone 16 Pro Max 256GB',  '2025-01-15'),
('Marie-Claire Fouda','Yaounde',   5, 'Samsung Galaxy S25 Ultra pour mon mari. Livraison rapide, bien emballe. Prix honnete et facture remise.',                 'Samsung Galaxy S25 Ultra', '2025-01-28'),
('Patrice Nkomo',     'Douala',    5, 'Troisieme achat chez Karim Telecom ! Toujours satisfait. Suivi WhatsApp top, tres professionnel.',                       'iPhone 15 128GB',          '2025-02-03'),
('Aissatou Bello',    'Garoua',    4, 'Bonne experience. Tecno Spark 20 pour mon fils. Livraison a Garoua en 2 jours. Garantie incluse.',                       'Tecno Spark 20',           '2025-02-10'),
('Jean-Pierre Essomba','Bafoussam',5, 'L equipe m a conseille honnêtement. Pris l iPhone 17 Pro. Super qualite, boite scellee, facture officielle.',            'iPhone 17 Pro 256GB',      '2025-02-18'),
('Sylvie Mengue',     'Yaounde',   5, 'AirPods Pro 2 recus en parfait etat ! Paiement a la livraison comme promis. Service client disponible le weekend.',      'AirPods Pro 2',            '2025-03-01');

-- Articles
INSERT IGNORE INTO articles (titre, slug, extrait, contenu, categorie, temps_lecture, date_publication) VALUES
('Les meilleurs iPhones au Cameroun en 2025', 'meilleurs-iphones-cameroun-2025',
'Notre guide complet pour choisir le bon iPhone selon votre budget au Cameroun.',
'<h2>Les meilleurs iPhones au Cameroun en 2025</h2><p>Le marche du smartphone au Cameroun evolue rapidement. Voici notre guide pour choisir le bon modele.</p><h3>iPhone 17 Pro Max — 1 250 000 FCFA</h3><p>Le summum de la technologie Apple. Ecran 6,9 pouces OLED, puce A19 Pro, camera ProRes 8K.</p><h3>iPhone 15 — 720 000 FCFA</h3><p>L entree ideale dans l univers Apple avec Dynamic Island et USB-C.</p><h3>Conseils achat</h3><ul><li>Achetez chez un revendeur de confiance</li><li>Exigez la boite scellee originale</li><li>Demandez une facture officielle</li></ul>',
'Guide achat', 8, '2025-01-10'),

('iPhone vs Samsung au Cameroun', 'iphone-vs-samsung',
'Comparatif honnete entre iPhone et Samsung pour le marche camerounais.',
'<h2>iPhone vs Samsung au Cameroun</h2><p>La grande question ! Voici notre comparatif adapte aux realites camerounaises.</p><h3>Prix</h3><p>Samsung couvre tous les budgets. iPhone commence a 720 000 FCFA.</p><h3>Longevite</h3><p>iPhone recoit des mises a jour pendant 6-7 ans. Meilleure valeur de revente.</p><h3>Notre recommandation</h3><p>Choisissez iPhone pour la longevite. Samsung pour le budget ou le S Pen.</p>',
'Comparatif', 10, '2025-01-22'),

('Meilleurs telephones moins de 200 000 FCFA', 'telephones-moins-200000-fcfa',
'Notre selection des meilleurs smartphones a petit budget au Cameroun.',
'<h2>Top smartphones moins de 200 000 FCFA</h2><h3>Tecno Spark 20 (95 000 FCFA)</h3><ul><li>Ecran 6,56 pouces 90Hz</li><li>Batterie 5000 mAh</li><li>Camera 50MP</li><li>128GB stockage</li></ul><h3>Infinix Hot 40 (85 000 FCFA)</h3><p>Grand ecran 6,78 pouces, batterie massive, double SIM.</p>',
'Guide achat', 7, '2025-02-05'),

('Samsung S25 Ultra vs iPhone 17 Pro Max', 'samsung-s25-ultra-vs-iphone-17-pro-max',
'Le duel au sommet entre les deux smartphones les plus puissants de 2025.',
'<h2>Samsung S25 Ultra vs iPhone 17 Pro Max</h2><h3>Prix chez Karim Telecom</h3><ul><li>iPhone 17 Pro Max : 1 250 000 FCFA</li><li>Samsung Galaxy S25 Ultra : 1 150 000 FCFA</li></ul><h3>Performances</h3><p>iPhone A19 Pro domine les benchmarks. S25 Ultra excellent pour le multitache.</p><h3>Photo</h3><p>Samsung : 200MP, zoom 10x. iPhone : ProRes 8K, couleurs naturelles.</p><h3>Verdict</h3><p>iPhone pour la video. Samsung pour la productivite et le zoom.</p>',
'Comparatif', 12, '2025-02-25');
