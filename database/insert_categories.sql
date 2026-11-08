USE karim_telecom;

-- Catégories par défaut (marques + accessoires)
INSERT INTO categories (nom, slug, image, ordre) VALUES
('Apple',     'apple',     '', 1),
('Samsung',   'samsung',   '', 2),
('Xiaomi',    'xiaomi',    '', 3),
('Google',    'google',    '', 4),
('Tecno',     'tecno',     '', 5),
('Infinix',   'infinix',   '', 6),
('Accessoires','accessoire', '', 7)
ON DUPLICATE KEY UPDATE nom = VALUES(nom);
