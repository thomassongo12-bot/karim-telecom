-- =============================================
-- Karim Telecom — Base de données
-- =============================================

CREATE DATABASE IF NOT EXISTS karim_telecom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE karim_telecom;

-- ----------------------
-- TABLE : produits
-- ----------------------
CREATE TABLE IF NOT EXISTS produits (
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
) ENGINE=InnoDB;

-- ----------------------
-- TABLE : avis
-- ----------------------
CREATE TABLE IF NOT EXISTS avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    note INT NOT NULL DEFAULT 5,
    texte TEXT NOT NULL,
    produit_achete VARCHAR(255) DEFAULT '',
    date_avis DATE NOT NULL
) ENGINE=InnoDB;

-- ----------------------
-- TABLE : articles
-- ----------------------
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    extrait TEXT,
    contenu LONGTEXT,
    categorie VARCHAR(100) DEFAULT 'Guide',
    temps_lecture INT DEFAULT 5,
    date_publication DATE NOT NULL
) ENGINE=InnoDB;

-- ----------------------
-- TABLE : villes
-- ----------------------
CREATE TABLE IF NOT EXISTS villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    delai_livraison VARCHAR(100) DEFAULT 'Livraison en 2-3 jours',
    emoji VARCHAR(10) DEFAULT '📍'
) ENGINE=InnoDB;

-- ----------------------
-- TABLE : produit_villes
-- ----------------------
CREATE TABLE IF NOT EXISTS produit_villes (
    produit_id INT NOT NULL,
    ville_slug VARCHAR(100) NOT NULL,
    PRIMARY KEY (produit_id, ville_slug)
) ENGINE=InnoDB;

-- =============================================
-- DONNÉES : Produits
-- =============================================
INSERT INTO produits (nom, slug, marque, prix, categorie, stockage, couleur, etat, garantie, disponibilite, en_stock, image, description, vedette) VALUES
('iPhone 17 Pro Max 256GB', 'iphone-17-pro-max', 'Apple', 1250000, 'telephone', '256GB', 'Titane Naturel', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'L\'iPhone 17 Pro Max redéfinit les standards du smartphone. Puce A19 Pro, écran 6,9 pouces OLED, caméra ProRes 8K. Le meilleur iPhone jamais fabriqué, disponible chez Karim Telecom à Douala.', 1),
('iPhone 17 Pro 256GB', 'iphone-17-pro', 'Apple', 1050000, 'telephone', '256GB', 'Titane Blanc', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'L\'iPhone 17 Pro en format compact 6,3 pouces. Puce A19 Pro, Dynamic Island, caméra 48MP. Performances Pro dans un format plus maniable.', 1),
('iPhone 16 Pro Max 256GB', 'iphone-16-pro-max', 'Apple', 950000, 'telephone', '256GB', 'Titane Désert', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'L\'iPhone 16 Pro Max avec Apple Intelligence. Écran ProMotion 120Hz 6,9 pouces, puce A18 Pro, zoom 5x. Le choix premium accessible.', 1),
('Samsung Galaxy S25 Ultra', 'samsung-galaxy-s25-ultra', 'Samsung', 1150000, 'telephone', '256GB', 'Titanium Black', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Galaxy S25 Ultra avec S Pen intégré. Snapdragon 8 Elite, caméra 200MP, zoom 10x, Galaxy AI. Le flagship Android ultime de 2025.', 1),
('Samsung Galaxy A56', 'samsung-galaxy-a56', 'Samsung', 320000, 'telephone', '128GB', 'Awesome Lilac', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Samsung Galaxy A56 : écran AMOLED 120Hz, triple caméra 50MP, IP67 résistant à l\'eau. Le meilleur milieu de gamme Samsung disponible au Cameroun.', 1),
('Google Pixel 9', 'google-pixel-9', 'Google', 680000, 'telephone', '128GB', 'Obsidian', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Google Pixel 9 avec Gemini AI intégré. Les meilleures photos computationnelles du marché, 7 ans de mises à jour garanties.', 0),
('iPhone 15 128GB', 'iphone-15', 'Apple', 720000, 'telephone', '128GB', 'Noir', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'L\'iPhone 15 avec Dynamic Island et USB-C. Puce A16 Bionic, caméra 48MP. L\'entrée idéale dans l\'univers Apple.', 1),
('Samsung Galaxy S24', 'samsung-galaxy-s24', 'Samsung', 780000, 'telephone', '256GB', 'Onyx Black', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Galaxy S24 avec Galaxy AI. Snapdragon 8 Gen 3, écran AMOLED 6,2 pouces 120Hz, 7 ans de mises à jour. Un investissement durable.', 0),
('Xiaomi 14', 'xiaomi-14', 'Xiaomi', 450000, 'telephone', '256GB', 'Noir', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Le Xiaomi 14 avec triple caméra Leica et Snapdragon 8 Gen 3. Charge ultra-rapide 90W. Performance flagship à prix compétitif.', 0),
('Tecno Spark 20', 'tecno-spark-20', 'Tecno', 95000, 'telephone', '128GB', 'Magic Skin White', 'Neuf', '6 mois', 'Douala', 1, '', 'Le Tecno Spark 20 : grand écran 6,56 pouces, batterie 5000 mAh, caméra 50MP. Le choix idéal pour les étudiants camerounais avec un petit budget.', 1),
('Infinix Hot 40', 'infinix-hot-40', 'Infinix', 85000, 'telephone', '128GB', 'Starfall Green', 'Neuf', '6 mois', 'Douala', 1, '', 'L\'Infinix Hot 40 : écran 6,78 pouces, batterie 5000 mAh, double SIM. Le smartphone le plus abordable avec un grand écran au Cameroun.', 1),
('AirPods Pro 2', 'airpods-pro-2', 'Apple', 180000, 'accessoire', NULL, 'Blanc', 'Neuf', '1 an constructeur', 'Douala', 1, '', 'Les AirPods Pro 2 avec réduction de bruit active H2. Son spatial, 30h d\'autonomie avec boîtier, résistant à l\'eau IP54.', 1);

-- =============================================
-- DONNÉES : Villes
-- =============================================
INSERT INTO villes (nom, slug, description, delai_livraison, emoji) VALUES
('Douala', 'douala', 'Karim Telecom est basé à Douala, capitale économique du Cameroun. Livraison le jour même dans tous les quartiers : Akwa, Bonanjo, Bonapriso, Makepe, Logbessou, Ndokoti et bien d\'autres.', 'Livraison le jour même (J+0)', '🏙️'),
('Yaoundé', 'yaounde', 'Nous livrons à Yaoundé, capitale politique du Cameroun. Nos produits partent de Douala avec des partenaires logistiques fiables. Commandez le matin, recevez demain.', 'Livraison en 24-48h', '🏛️'),
('Bafoussam', 'bafoussam', 'Karim Telecom dessert Bafoussam et toute la région de l\'Ouest. Nos produits sont expédiés via nos partenaires. Paiement à la livraison disponible.', 'Livraison en 2-3 jours', '🌿'),
('Bamenda', 'bamenda', 'Nous livrons dans la région du Nord-Ouest, à Bamenda et environs. Tous nos téléphones sont disponibles avec garantie et facture officielle.', 'Livraison en 2-3 jours', '⛰️'),
('Garoua', 'garoua', 'Karim Telecom livre dans le Nord du Cameroun, à Garoua. Contactez-nous sur WhatsApp pour planifier votre livraison et choisir votre point de retrait.', 'Livraison en 3-4 jours', '☀️');

-- =============================================
-- DONNÉES : Produit-Villes
-- =============================================
INSERT INTO produit_villes (produit_id, ville_slug) VALUES
(1,'douala'),(1,'yaounde'),
(2,'douala'),(2,'yaounde'),(2,'bafoussam'),
(3,'douala'),(3,'yaounde'),(3,'bafoussam'),
(4,'douala'),(4,'yaounde'),
(5,'douala'),(5,'yaounde'),(5,'bafoussam'),(5,'bamenda'),(5,'garoua'),
(6,'douala'),(6,'yaounde'),
(7,'douala'),(7,'yaounde'),(7,'bafoussam'),
(8,'douala'),(8,'yaounde'),(8,'bafoussam'),
(9,'douala'),(9,'yaounde'),
(10,'douala'),(10,'yaounde'),(10,'bafoussam'),(10,'bamenda'),(10,'garoua'),
(11,'douala'),(11,'yaounde'),(11,'bafoussam'),(11,'bamenda'),(11,'garoua'),
(12,'douala'),(12,'yaounde');

-- =============================================
-- DONNÉES : Avis clients
-- =============================================
INSERT INTO avis (nom, ville, note, texte, produit_achete, date_avis) VALUES
('Armand Tchamba', 'Douala', 5, 'Excellent service ! J\'ai commandé un iPhone 16 Pro Max le matin et reçu l\'après-midi à Akwa. Téléphone 100% original, boîte scellée. Je recommande fortement Karim Telecom !', 'iPhone 16 Pro Max 256GB', '2025-01-15'),
('Marie-Claire Fouda', 'Yaoundé', 5, 'J\'ai offert un Samsung Galaxy S25 Ultra à mon mari. L\'équipe m\'a aidée à choisir. Livraison rapide à Yaoundé, bien emballé. Prix honnête et facture remise. Parfait !', 'Samsung Galaxy S25 Ultra', '2025-01-28'),
('Patrice Nkomo', 'Douala', 5, 'Troisième achat chez Karim Telecom ! Toujours satisfait. iPhone 15 pour ma fille étudiante. Le suivi WhatsApp est top, réponse rapide et très professionnel.', 'iPhone 15 128GB', '2025-02-03'),
('Aïssatou Bello', 'Garoua', 4, 'Bonne expérience. Tecno Spark 20 pour mon fils. Livraison à Garoua en 2 jours, bien tenu informée. Téléphone fonctionne parfaitement, garantie incluse.', 'Tecno Spark 20', '2025-02-10'),
('Jean-Pierre Essomba', 'Bafoussam', 5, 'L\'équipe m\'a conseillé honnêtement entre iPhone et Samsung. J\'ai pris l\'iPhone 17 Pro. Super qualité, boîte scellée, facture officielle. Bravo Karim Telecom !', 'iPhone 17 Pro 256GB', '2025-02-18'),
('Sylvie Mengue', 'Yaoundé', 5, 'AirPods Pro 2 reçus en parfait état ! Paiement à la livraison comme promis. Emballage Apple original intact. Service client disponible même le weekend.', 'AirPods Pro 2', '2025-03-01');

-- =============================================
-- DONNÉES : Articles
-- =============================================
INSERT INTO articles (titre, slug, extrait, contenu, categorie, temps_lecture, date_publication) VALUES
('Les meilleurs iPhones à acheter au Cameroun en 2025',
'meilleurs-iphones-cameroun-2025',
'Notre guide complet pour choisir le bon iPhone selon votre budget au Cameroun. Comparatif des modèles disponibles à Douala avec prix en FCFA.',
'<h2>Les meilleurs iPhones au Cameroun en 2025</h2>
<p>Le marché du smartphone au Cameroun évolue rapidement. Les iPhones sont de plus en plus accessibles à Douala et Yaoundé. Voici notre guide pour choisir le bon modèle.</p>
<h3>iPhone 17 Pro Max — Le meilleur de 2025 (1 250 000 FCFA)</h3>
<p>L\'iPhone 17 Pro Max est le summum de la technologie Apple. Son écran 6,9 pouces OLED, sa puce A19 Pro et son système de caméra ProRes 8K en font le smartphone le plus puissant du marché.</p>
<p><strong>Pour qui ?</strong> Les professionnels, créateurs de contenu et passionnés de photo.</p>
<h3>iPhone 17 Pro — Format compact premium (1 050 000 FCFA)</h3>
<p>Si vous voulez toutes les fonctionnalités Pro sans l\'encombrement du Max, l\'iPhone 17 Pro à 6,3 pouces est votre choix. Même puce A19 Pro, même qualité photo.</p>
<h3>iPhone 15 — L\'entrée accessible (720 000 FCFA)</h3>
<p>L\'iPhone 15 avec Dynamic Island et USB-C est la meilleure entrée de gamme Apple. Il offre une expérience iOS complète à un prix plus accessible.</p>
<h3>Conseils pour acheter un iPhone au Cameroun</h3>
<ul>
<li>Achetez toujours chez un revendeur de confiance comme Karim Telecom</li>
<li>Exigez la boîte scellée originale Apple</li>
<li>Demandez une facture officielle pour la garantie</li>
<li>Vérifiez le numéro IMEI sur le site Apple</li>
<li>Préférez le paiement à la livraison</li>
</ul>',
'Guide d\'achat', 8, '2025-01-10'),

('iPhone vs Samsung : lequel choisir au Cameroun ?',
'iphone-vs-samsung',
'Comparatif honnête entre iPhone et Samsung pour le marché camerounais. Prix, durabilité, après-vente, valeur de revente — tout ce que vous devez savoir.',
'<h2>iPhone vs Samsung au Cameroun</h2>
<p>La grande question ! iPhone ou Samsung ? Voici notre comparatif adapté aux réalités du marché camerounais.</p>
<h3>1. Prix et rapport qualité/prix</h3>
<p>Samsung remporte ce round grâce à sa gamme étendue. Du Galaxy A56 à 320 000 FCFA jusqu\'au S25 Ultra à 1 150 000 FCFA, Samsung couvre tous les budgets.</p>
<p>iPhone commence à 720 000 FCFA (iPhone 15). L\'entrée de gamme Apple est plus chère.</p>
<h3>2. Durabilité et longévité</h3>
<p>Apple fait la différence ici. Un iPhone reçoit des mises à jour pendant 6 à 7 ans contre 3 à 4 ans pour Samsung. Meilleure valeur de revente aussi.</p>
<h3>3. Appareil photo</h3>
<p>Samsung : couleurs saturées et vives, parfaites pour Instagram. iPhone : couleurs naturelles, excellent pour la vidéo professionnelle. Les deux sont excellents.</p>
<h3>Notre recommandation</h3>
<p><strong>Choisissez iPhone si</strong> vous avez le budget, voulez la meilleure longévité et créez du contenu vidéo.</p>
<p><strong>Choisissez Samsung si</strong> vous préférez Android, avez un budget intermédiaire ou voulez le S Pen.</p>',
'Comparatif', 10, '2025-01-22'),

('Meilleurs téléphones à moins de 200 000 FCFA',
'telephones-moins-200000-fcfa',
'Notre sélection des meilleurs smartphones à petit budget au Cameroun. Qualité garantie sans dépenser une fortune.',
'<h2>Top smartphones à moins de 200 000 FCFA</h2>
<p>Vous cherchez un bon smartphone sans dépasser 200 000 FCFA ? Voici notre sélection pour le marché camerounais.</p>
<h3>Tecno Spark 20 — Notre top choix (95 000 FCFA)</h3>
<p>Le Tecno Spark 20 est notre recommandation n°1 dans cette gamme.</p>
<ul>
<li>Écran 6,56 pouces 90Hz fluide</li>
<li>Batterie 5000 mAh — 2 jours d\'autonomie</li>
<li>Caméra 50MP pour les réseaux sociaux</li>
<li>128GB de stockage</li>
<li>Prix imbattable : 95 000 FCFA</li>
</ul>
<h3>Infinix Hot 40 (85 000 FCFA)</h3>
<p>L\'Infinix Hot 40 est encore moins cher et offre un grand écran 6,78 pouces pour YouTube et TikTok. Batterie massive, double SIM, Android récent.</p>
<h3>Nos conseils</h3>
<ul>
<li>Évitez les contrefaçons — méfiez-vous des prix trop bas</li>
<li>Exigez une garantie minimum de 6 mois</li>
<li>Testez avant d\'acheter chez Karim Telecom</li>
</ul>',
'Guide d\'achat', 7, '2025-02-05'),

('Meilleurs téléphones pour étudiants au Cameroun',
'meilleurs-telephones-etudiants-cameroun',
'Guide spécial étudiants camerounais. Nos recommandations par budget pour rester connecté pendant les études.',
'<h2>Les meilleurs téléphones pour étudiants au Cameroun</h2>
<p>Être étudiant en 2025 nécessite un smartphone performant pour les cours en ligne, WhatsApp, YouTube et les recherches.</p>
<h3>Ce dont un étudiant a vraiment besoin</h3>
<ul>
<li>Bonne batterie pour tenir toute la journée de cours</li>
<li>Au minimum 128GB de stockage pour les PDFs</li>
<li>Performance correcte pour Zoom et les applis universitaires</li>
<li>Bon rapport qualité/prix</li>
</ul>
<h3>Budget moins de 100 000 FCFA : Infinix Hot 40 (85 000 FCFA)</h3>
<p>Grand écran, batterie massive, double SIM. Parfait pour le premier téléphone étudiant.</p>
<h3>Budget 300 000-400 000 FCFA : Samsung Galaxy A56 (320 000 FCFA)</h3>
<p>Avec Android 15 et 4 ans de mises à jour, il durera toute la durée de vos études. Écran AMOLED, IP67, caméra 50MP pour scanner les cours.</p>
<h3>Conseil Karim Telecom</h3>
<p>Venez en boutique tester les appareils avant d\'acheter. Paiement à la livraison disponible. Facture officielle fournie.</p>',
'Guide d\'achat', 9, '2025-03-05'),

('Samsung S25 Ultra vs iPhone 17 Pro Max',
'samsung-s25-ultra-vs-iphone-17-pro-max',
'Le duel au sommet entre les deux smartphones les plus puissants de 2025. Lequel vaut votre argent au Cameroun ?',
'<h2>Samsung S25 Ultra vs iPhone 17 Pro Max</h2>
<p>Deux géants, deux philosophies. Voici le comparatif pour le marché camerounais.</p>
<h3>Les prix chez Karim Telecom</h3>
<ul>
<li>iPhone 17 Pro Max : 1 250 000 FCFA</li>
<li>Samsung Galaxy S25 Ultra : 1 150 000 FCFA</li>
</ul>
<h3>Écrans</h3>
<p>Les deux proposent des écrans OLED 6,9 pouces 120Hz. iPhone légèrement supérieur en extérieur. Samsung plus vif pour le streaming.</p>
<h3>Performances</h3>
<p>iPhone 17 Pro Max avec puce A19 Pro domine les benchmarks. Galaxy S25 Ultra avec Snapdragon 8 Elite est excellent pour le multitâche.</p>
<h3>Appareil photo</h3>
<p>Samsung : 200MP, zoom 10x, parfait pour Instagram. iPhone : ProRes 8K, couleurs naturelles, idéal pour la vidéo.</p>
<h3>L\'avantage exclusif Samsung</h3>
<p>Le S Pen intégré est unique. Pour les professionnels qui prennent des notes ou font des schémas, c\'est décisif.</p>
<h3>Notre verdict</h3>
<p>Choisissez iPhone pour la vidéo et la longévité. Choisissez Samsung pour la productivité et le zoom.</p>',
'Comparatif', 12, '2025-02-25'),

('Meilleurs téléphones à moins de 300 000 FCFA',
'telephones-moins-300000-fcfa',
'Le milieu de gamme offre des expériences proches du flagship. Notre sélection entre 200 000 et 300 000 FCFA.',
'<h2>Top smartphones moins de 300 000 FCFA</h2>
<p>La tranche 200 000 - 300 000 FCFA est la plus compétitive du marché camerounais. Équilibre idéal performance/budget.</p>
<h3>Samsung Galaxy A56 — Notre coup de cœur (320 000 FCFA)</h3>
<p>Légèrement au-dessus des 300 000 FCFA mais il vaut chaque franc :</p>
<ul>
<li>Écran Super AMOLED 6,7 pouces 120Hz</li>
<li>IP67 résistant à l\'eau</li>
<li>Triple caméra 50MP</li>
<li>8GB RAM avec Android 15</li>
<li>4 ans de mises à jour garanties</li>
</ul>
<h3>Xiaomi 14 (450 000 FCFA)</h3>
<p>Si vous voulez le meilleur processeur, le Xiaomi 14 avec Snapdragon 8 Gen 3 est votre choix. Caméra Leica, charge 90W.</p>
<h3>Critères de choix</h3>
<ul>
<li>Écran AMOLED de préférence</li>
<li>Minimum 5000 mAh de batterie</li>
<li>Au moins 128GB de stockage</li>
<li>Garantie 1 an minimum</li>
</ul>',
'Guide d\'achat', 8, '2025-02-15');
