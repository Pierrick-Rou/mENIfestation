INSERT INTO Ville (nom,code_Postal) VALUES ('Saint-Herblain','44800'),
                                        ('Nantes','44100'),
                                        ('Chartres De Bretagne', '35131'),
                                        ('La Roche Sur Yon','85000'),
                                        ('Paris', '75000'),
                                        ('Lyon', '69000'),
                                        ('Marseille', '13000'),
                                        ('Toulouse', '31000'),
                                        ('Nantes', '44000'),
                                        ('Lille', '59000');




INSERT INTO SITE (nom) VALUES ('ENI-Saint-Herblain'),('ENI-Chartres'),('ENI_LRSY');

INSERT INTO ETAT (libelle) VALUES ('Créée'), ('Ouverte'),('Clôturée'),('Activité en cours'),('Passée'),('Annulée');


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

