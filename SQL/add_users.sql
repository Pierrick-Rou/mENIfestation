INSERT INTO Ville (nom,code_Postal) VALUES
    ('Saint-Herblain','44800'),
    ('Nantes','44100'),
    ('Chartres De Bretagne', '35131'),
    ('La Roche Sur Yon','85000'),
    ('Paris', '75000'),
    ('Lyon', '69000'),
    ('Marseille', '13000'),
    ('Toulouse', '31000'),
    ('Nantes', '44000'),
    ('Lille', '59000');


INSERT INTO site (id, nom) VALUES
    (1, 'CGT'),
    (2, 'CFDT'),
    (3, 'FO'),
    (4, 'Solidaires'),
    (5, 'FSU');



INSERT INTO participant (email,site_id,roles,password,nom,prenom,telephone,administrateur,actif) VALUES
    ('admin@menifestation.com',1,JSON_ARRAY('ROLE_ADMIN'),'$2y$13$8OgVsX8l2cefcTYFnwkem./gL3fyqkZ/3SnA0I7va2YF9G0zoSFM2','admin','admin','0000000000',1,1),
    ('membre@menifestation.com',2,JSON_ARRAY('ROLE_USER'),'$2y$13$37edNM40O482PEof5n7b2OdCJaYKvgC5Sw1nCQcvGlWbCxuBc5PI2','membre','membre','0000000000',0,1);

INSERT INTO lieu (nom, rue, latitude, longitude, ville_id) VALUES
    ('Place de la République', 'Place de la République', 48.8674, 2.3630, 4),
    ( 'Place de la Bastille', 'Place de la Bastille', 48.8530, 2.3690, 4),
    ( 'Hôtel de Ville', 'Place de l\'Hôtel de Ville', 45.7578, 4.8320, 5),
    ( 'Vieux-Port', 'Quai du Port', 43.2965, 5.3698, 6),
    ( 'Capitole', 'Place du Capitole', 43.6045, 1.4442, 7),
    ( 'Place Royale', 'Place Royale', 47.2130, -1.5586, 8),
    ( 'Grand-Place', 'Grand-Place', 50.6366, 3.0633, 9),
    ( 'Nation', 'Place de la Nation', 48.8485, 2.3950, 4),
    ('Bellecour', 'Place Bellecour', 45.7570, 4.8327, 5),
    ('Préfecture', 'Rue de la Préfecture', 43.2985, 5.3760, 6),
    ( 'Campus ENI Saint-Herblain', '3 Rue Michael Faraday', 47.225, -1.6178, 1),
    ('Campus ENI Chartres De Bretagne', '8 Rue Léo Lagrange', 48.0389 , -1.6920, 2),
    ('Campus ENI La Roche Sur Yon', 'Boulevard Rivoli', 46.6743, -1.4158, 3);

INSERT INTO sortie (id, nom, date_heure_debut, duree, date_limite_inscription, nb_inscription_max, infos_sortie, organisateur_id, lieu_id, site_id, etat) VALUES
    (1, 'Manifestation Nationale CGT', '2025-09-15 14:00:00', '02:00:00', '2025-09-10 23:59:59', 100, 'Manifestation contre la réforme des retraites', 1, 1, 1, 'ouverte'),
    (2, 'Marche Solidaire CFDT', '2025-10-01 10:00:00', '03:00:00', '2025-09-25 23:59:59', 80, 'Marche en soutien aux travailleurs précaires', 2, 2, 2, 'en cours'),
    (3, 'Rassemblement FO', '2025-08-25 17:00:00', '01:30:00', '2025-08-20 23:59:59', 60, 'Rassemblement pour la défense des droits syndicaux', 1, 4, 3, 'terminée'),
    (4, 'Action Solidaires Éducation', '2025-09-20 09:00:00', '04:00:00', '2025-09-15 23:59:59', 70, 'Blocage symbolique des rectorats', 2, 5, 4, 'créée'),
    (5, 'Grève Générale FSU', '2025-10-05 08:00:00', '08:00:00', '2025-10-01 23:59:59', 200, 'Grève nationale dans les établissements scolaires', 2, 6, 5, 'cloturée'),
    (6, 'Manifestation intersyndicale', '2025-09-01 15:00:00', '02:00:00', '2025-08-28 23:59:59', 150, 'Manifestation rassemblant CGT, CFDT et FO', 1, 8, 1, 'annulée'),
    (7, 'Rassemblement pour la paix', '2025-09-10 14:00:00', '03:00:00', '2025-09-05 23:59:59', 120, 'Rassemblement intersyndical pour la paix dans le monde.', 1, 1, 1, 'ouverte'),
    (8, 'Débat public sur les retraites', '2025-08-28 18:00:00', '02:30:00', '2025-08-25 23:59:59', 50, 'Débat ouvert aux citoyens sur la réforme des retraites.', 2, 9, 2, 'cloturée'),
    (9, 'Opération "Ville morte"', '2025-09-03 06:00:00', '06:00:00', '2025-08-30 23:59:59', 200, 'Blocage des axes routiers pour protester contre la précarité.', 1, 7, 3, 'annulée'),
    (10, 'AG intersyndicale enseignants', '2025-09-05 17:30:00', '02:00:00', '2025-09-01 23:59:59', 100, 'Assemblée Générale pour discuter des revendications enseignants.', 1, 6, 5, 'créée'),
    (11, 'Marche des soignants', '2025-08-26 09:00:00', '04:00:00', '2025-08-22 23:59:59', 150, 'Marche de soutien aux personnels soignants.', 2, 3, 4, 'terminée'),
    (12, 'Distribution de tracts', '2025-08-27 08:30:00', '01:00:00', '2025-08-26 23:00:00', 30, 'Action symbolique dans les gares.', 1, 10, 2, 'en cours');
