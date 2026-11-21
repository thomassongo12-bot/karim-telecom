-- =============================================
-- Fix : convertir ENUM en VARCHAR pour categorie
-- A importer dans phpMyAdmin Hostinger
-- =============================================

ALTER TABLE produits 
  MODIFY COLUMN categorie VARCHAR(100) NOT NULL DEFAULT 'telephone';

-- Vérification
SELECT DISTINCT categorie FROM produits;
