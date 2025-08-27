INSERT INTO Ville (nom,code_Postal) VALUES ('Saint-Herblain','44800'),
                                          ('Nantes','44100'),
                                          ('Chartres De Bretagne', '35131'),
                                          ('La Roche Sur Yon','85000');
INSERT INTO SITE (nom) VALUES ('ENI-Saint-Herblain'),('ENI-Chartres'),('ENI_LRSY');


INSERT INTO participant (email,site_id,roles,password,nom,prenom,telephone,administrateur,actif) VALUES
                                                                                                               ('admin@menifestation.com',1,JSON_ARRAY('ROLE_ADMIN'),'$2y$13$8OgVsX8l2cefcTYFnwkem./gL3fyqkZ/3SnA0I7va2YF9G0zoSFM2','admin','admin','0000000000',1,1),
                                                                                                               ('membre@menifestation.com',2,JSON_ARRAY('ROLE_USER'),'$2y$13$37edNM40O482PEof5n7b2OdCJaYKvgC5Sw1nCQcvGlWbCxuBc5PI2','membre','membre','0000000000',0,1)
