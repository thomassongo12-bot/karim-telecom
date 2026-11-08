USE karim_telecom;

-- PRODUITS
INSERT INTO produits (nom, slug, marque, prix, categorie, stockage, couleur, etat, garantie, disponibilite, en_stock, description, vedette) VALUES
('iPhone 17 Pro Max 256GB','iphone-17-pro-max','Apple',1250000,'telephone','256GB','Titane Naturel','Neuf','1 an constructeur','Douala',1,'L\'iPhone 17 Pro Max redefinit les standards. Puce A19 Pro, ecran 6.9 pouces OLED, camera ProRes 8K. Disponible chez Karim Telecom Douala.',1),
('iPhone 17 Pro 256GB','iphone-17-pro','Apple',1050000,'telephone','256GB','Titane Blanc','Neuf','1 an constructeur','Douala',1,'L\'iPhone 17 Pro format compact 6.3 pouces. Puce A19 Pro, Dynamic Island, camera 48MP.',1),
('iPhone 16 Pro Max 256GB','iphone-16-pro-max','Apple',950000,'telephone','256GB','Titane Desert','Neuf','1 an constructeur','Douala',1,'iPhone 16 Pro Max avec Apple Intelligence. Ecran ProMotion 120Hz 6.9 pouces, puce A18 Pro.',1),
('Samsung Galaxy S25 Ultra','samsung-galaxy-s25-ultra','Samsung',1150000,'telephone','256GB','Titanium Black','Neuf','1 an constructeur','Douala',1,'Galaxy S25 Ultra avec S Pen integre. Snapdragon 8 Elite, camera 200MP, zoom 10x, Galaxy AI.',1),
('Samsung Galaxy A56','samsung-galaxy-a56','Samsung',320000,'telephone','128GB','Awesome Lilac','Neuf','1 an constructeur','Douala',1,'Samsung Galaxy A56 : ecran AMOLED 120Hz, triple camera 50MP, IP67. Meilleur milieu de gamme.',1),
('Google Pixel 9','google-pixel-9','Google',680000,'telephone','128GB','Obsidian','Neuf','1 an constructeur','Douala',1,'Google Pixel 9 avec Gemini AI. Meilleures photos computationnelles, 7 ans de mises a jour.',0),
('iPhone 15 128GB','iphone-15','Apple',720000,'telephone','128GB','Noir','Neuf','1 an constructeur','Douala',1,'iPhone 15 avec Dynamic Island et USB-C. Puce A16 Bionic, camera 48MP. Entree Apple accessible.',1),
('Samsung Galaxy S24','samsung-galaxy-s24','Samsung',780000,'telephone','256GB','Onyx Black','Neuf','1 an constructeur','Douala',1,'Galaxy S24 avec Galaxy AI. Snapdragon 8 Gen 3, ecran AMOLED 6.2 pouces 120Hz, 7 ans updates.',0),
('Xiaomi 14','xiaomi-14','Xiaomi',450000,'telephone','256GB','Noir','Neuf','1 an constructeur','Douala',1,'Xiaomi 14 avec triple camera Leica et Snapdragon 8 Gen 3. Charge ultra-rapide 90W.',0),
('Tecno Spark 20','tecno-spark-20','Tecno',95000,'telephone','128GB','Magic Skin White','Neuf','6 mois','Douala',1,'Tecno Spark 20 : grand ecran 6.56 pouces, batterie 5000 mAh, camera 50MP. Ideal etudiants.',1),
('Infinix Hot 40','infinix-hot-40','Infinix',85000,'telephone','128GB','Starfall Green','Neuf','6 mois','Douala',1,'Infinix Hot 40 : ecran 6.78 pouces, batterie 5000 mAh, double SIM. Smartphone abordable.',1),
('AirPods Pro 2','airpods-pro-2','Apple',180000,'accessoire',NULL,'Blanc','Neuf','1 an constructeur','Douala',1,'AirPods Pro 2 avec reduction de bruit active H2. Son spatial, 30h autonomie, IP54.',1);

-- VILLES
INSERT INTO villes (nom, slug, description, delai_livraison, emoji) VALUES
('Douala','douala','Karim Telecom est base a Douala. Livraison le jour meme dans tous les quartiers : Akwa, Bonanjo, Bonapriso, Makepe, Logbessou, Ndokoti.','Livraison le jour meme (J+0)','🏙️'),
('Yaounde','yaounde','Nous livrons a Yaounde, capitale politique. Nos produits partent de Douala avec des partenaires fiables. Commandez le matin, recevez demain.','Livraison en 24-48h','🏛️'),
('Bafoussam','bafoussam','Karim Telecom dessert Bafoussam et toute la region de l\'Ouest. Paiement a la livraison disponible.','Livraison en 2-3 jours','🌿'),
('Bamenda','bamenda','Nous livrons dans la region du Nord-Ouest, a Bamenda et environs. Garantie et facture officielle.','Livraison en 2-3 jours','⛰️'),
('Garoua','garoua','Karim Telecom livre dans le Nord du Cameroun, a Garoua. Contactez-nous sur WhatsApp pour planifier.','Livraison en 3-4 jours','☀️');

-- PRODUIT_VILLES
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

-- AVIS
INSERT INTO avis (nom, ville, note, texte, produit_achete, date_avis) VALUES
('Armand Tchamba','Douala',5,'Excellent service ! iPhone 16 Pro Max commande le matin, recu l\'apres-midi a Akwa. 100% original, boite scellee. Je recommande Karim Telecom !','iPhone 16 Pro Max','2025-01-15'),
('Marie-Claire Fouda','Yaounde',5,'Samsung Galaxy S25 Ultra pour mon mari. Livraison rapide a Yaounde, bien emballe. Prix honnete et facture remise. Parfait !','Samsung Galaxy S25 Ultra','2025-01-28'),
('Patrice Nkomo','Douala',5,'Troisieme achat chez Karim Telecom ! iPhone 15 pour ma fille. Suivi WhatsApp top, reponse rapide et tres professionnel.','iPhone 15 128GB','2025-02-03'),
('Aissatou Bello','Garoua',4,'Bonne experience. Tecno Spark 20 pour mon fils. Livraison a Garoua en 2 jours. Telephone fonctionne parfaitement.','Tecno Spark 20','2025-02-10'),
('Jean-Pierre Essomba','Bafoussam',5,'L\'equipe m\'a conseille honnêtement. Pris l\'iPhone 17 Pro. Super qualite, boite scellee, facture officielle.','iPhone 17 Pro 256GB','2025-02-18'),
('Sylvie Mengue','Yaounde',5,'AirPods Pro 2 recus en parfait etat ! Paiement a la livraison comme promis. Service client disponible le weekend.','AirPods Pro 2','2025-03-01');

-- ARTICLES
INSERT INTO articles (titre, slug, extrait, contenu, categorie, temps_lecture, date_publication) VALUES
('Les meilleurs iPhones au Cameroun en 2025','meilleurs-iphones-cameroun-2025','Guide complet pour choisir le bon iPhone selon votre budget au Cameroun. Comparatif des modeles disponibles a Douala.','<h2>Les meilleurs iPhones au Cameroun en 2025</h2><p>Le marche du smartphone au Cameroun evolue rapidement. Voici notre guide pour choisir le bon modele.</p><h3>iPhone 17 Pro Max - 1 250 000 FCFA</h3><p>Le summum de la technologie Apple. Ecran 6,9 pouces OLED, puce A19 Pro, camera ProRes 8K.</p><h3>iPhone 15 - 720 000 FCFA</h3><p>Entree ideale dans l\'univers Apple avec Dynamic Island et USB-C.</p><h3>Conseils achat</h3><ul><li>Achetez chez un revendeur de confiance</li><li>Exigez la boite scellee originale</li><li>Demandez une facture officielle</li><li>Preferez le paiement a la livraison</li></ul>','Guide achat',8,'2025-01-10'),

('iPhone vs Samsung au Cameroun','iphone-vs-samsung','Comparatif honnete entre iPhone et Samsung pour le marche camerounais. Prix, durabilite, valeur de revente.','<h2>iPhone vs Samsung au Cameroun</h2><p>La grande question ! Voici notre comparatif adapte aux realites camerounaises.</p><h3>Prix et rapport qualite/prix</h3><p>Samsung couvre tous les budgets du A56 a 320 000 FCFA au S25 Ultra a 1 150 000 FCFA. iPhone commence a 720 000 FCFA.</p><h3>Longevite</h3><p>iPhone recoit des mises a jour pendant 6-7 ans contre 3-4 ans pour Samsung. Meilleure valeur de revente.</p><h3>Notre recommandation</h3><p>Choisissez iPhone pour la longevite. Choisissez Samsung pour le budget ou si vous voulez le S Pen.</p>','Comparatif',10,'2025-01-22'),

('Meilleurs telephones moins de 200 000 FCFA','telephones-moins-200000-fcfa','Notre selection des meilleurs smartphones a petit budget au Cameroun. Qualite garantie sans se ruiner.','<h2>Top smartphones moins de 200 000 FCFA</h2><p>Voici notre selection pour le marche camerounais.</p><h3>Tecno Spark 20 - 95 000 FCFA</h3><ul><li>Ecran 6,56 pouces 90Hz</li><li>Batterie 5000 mAh - 2 jours d\'autonomie</li><li>Camera 50MP</li><li>128GB stockage</li></ul><h3>Infinix Hot 40 - 85 000 FCFA</h3><p>Grand ecran 6,78 pouces, batterie massive, double SIM, Android recent.</p><h3>Nos conseils</h3><ul><li>Evitez les contrefacons</li><li>Exigez une garantie minimum 6 mois</li><li>Testez avant d\'acheter chez Karim Telecom</li></ul>','Guide achat',7,'2025-02-05'),

('Meilleurs telephones pour etudiants','meilleurs-telephones-etudiants-cameroun','Guide special etudiants camerounais. Nos recommandations par budget pour rester connecte pendant les etudes.','<h2>Meilleurs telephones pour etudiants au Cameroun</h2><p>Un etudiant en 2025 a besoin d\'un bon smartphone pour les cours en ligne, WhatsApp et les recherches.</p><h3>Ce dont un etudiant a besoin</h3><ul><li>Bonne batterie pour la journee de cours</li><li>128GB de stockage minimum pour les PDFs</li><li>Performance correcte pour Zoom</li><li>Bon rapport qualite/prix</li></ul><h3>Budget moins de 100 000 FCFA : Infinix Hot 40 (85 000 FCFA)</h3><p>Grand ecran, batterie massive, double SIM. Parfait pour le premier telephone etudiant.</p><h3>Budget 300 000+ FCFA : Samsung Galaxy A56 (320 000 FCFA)</h3><p>Android 15, 4 ans de mises a jour, IP67. Durera toute la duree de vos etudes.</p>','Guide achat',9,'2025-03-05'),

('Samsung S25 Ultra vs iPhone 17 Pro Max','samsung-s25-ultra-vs-iphone-17-pro-max','Le duel au sommet entre les deux smartphones les plus puissants de 2025. Lequel vaut votre argent ?','<h2>Samsung S25 Ultra vs iPhone 17 Pro Max</h2><h3>Les prix chez Karim Telecom</h3><ul><li>iPhone 17 Pro Max : 1 250 000 FCFA</li><li>Samsung Galaxy S25 Ultra : 1 150 000 FCFA</li></ul><h3>Performances</h3><p>iPhone 17 Pro Max avec puce A19 Pro domine les benchmarks. S25 Ultra avec Snapdragon 8 Elite est excellent pour le multitache.</p><h3>Appareil photo</h3><p>Samsung : 200MP, zoom 10x, parfait pour Instagram. iPhone : ProRes 8K, couleurs naturelles, ideal video.</p><h3>Verdict</h3><p>Choisissez iPhone pour la video. Choisissez Samsung pour le S Pen et le zoom.</p>','Comparatif',12,'2025-02-25'),

('Meilleurs telephones moins de 300 000 FCFA','telephones-moins-300000-fcfa','Le milieu de gamme offre des experiences proches du flagship. Notre selection entre 200 000 et 300 000 FCFA.','<h2>Top smartphones moins de 300 000 FCFA</h2><p>La tranche 200 000 - 300 000 FCFA est la plus competitive du marche camerounais.</p><h3>Samsung Galaxy A56 - 320 000 FCFA</h3><ul><li>Ecran Super AMOLED 6,7 pouces 120Hz</li><li>IP67 resistant a l\'eau</li><li>Triple camera 50MP</li><li>8GB RAM Android 15</li><li>4 ans de mises a jour</li></ul><h3>Criteres de choix</h3><ul><li>Ecran AMOLED de preference</li><li>Minimum 5000 mAh de batterie</li><li>128GB de stockage</li><li>Garantie 1 an minimum</li></ul>','Guide achat',8,'2025-02-15');
