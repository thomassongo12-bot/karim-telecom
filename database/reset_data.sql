USE karim_telecom;

-- Supprimer toutes les données demo
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE produit_villes;
TRUNCATE TABLE produit_images;
TRUNCATE TABLE produits;
TRUNCATE TABLE avis;
TRUNCATE TABLE articles;
SET FOREIGN_KEY_CHECKS = 1;

-- Garder uniquement les villes (elles sont utiles)
-- Pour supprimer aussi les villes, décommentez :
-- TRUNCATE TABLE villes;
