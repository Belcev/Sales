CREATE DATABASE sales;

-- nastavení kodování pro vytvořenou databázi
ALTER DATABASE sales CHARACTER SET utf8 COLLATE utf8_general_ci;


CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    name VARCHAR(100) AS (concat(`first_name`,' ',`last_name`)) VIRTUAL NOT NULL,
    active BOOLEAN NOT NULL
);


CREATE TABLE sale (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by_id INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by_id INT NOT NULL,
    active_from DATETIME NOT NULL,
    active_to DATETIME NOT NULL,
    color VARCHAR(6) NOT NULL,
    FOREIGN KEY (created_by_id) REFERENCES user(id),
    FOREIGN KEY (updated_by_id) REFERENCES user(id)
);


CREATE TABLE tag (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE sale_tag (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sale(id),
    FOREIGN KEY (tag_id) REFERENCES tag(id)
);

INSERT INTO `user` (`id`, `username`, `password`, `role`, `first_name`, `last_name`, `active`) VALUES
    (NULL, 'admin', '$2y$10$yc6InJOxVWAom.3Xy9zloOoXKzqq/GIblSpuSmvqFTasP6woP.ota', 'admin', 'admin', 'admin', '1'), -- heslo: admin
    (NULL, 'user', '$2y$10$EPu1Vo0fxNy4Vxec3KivaeEAfS6PNv8Hg2nbqdFCSyXA2v.npXoXe', 'user', 'user', 'user', '0'); -- heslo: TajneHeslo !je potřeba Aktivovat!

INSERT INTO `tag` (`id`, `name`) VALUES (NULL, 'RPG'), (NULL, 'Survival'), (NULL, 'Open World'), (NULL, 'Platformer'), (NULL, '3D Střílečka');

ALTER TABLE sale AUTO_INCREMENT = 1;

INSERT INTO `sale` (`id`, `name`, `created_at`, `created_by_id`, `updated_at`, `updated_by_id`, `active_from`, `active_to`, `color`) VALUES
    (NULL, 'Zima 2024', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1', '2023-12-21 00:00:00.000000', '2024-03-21 00:00:00.000000', 'B3DAF1'),
    (NULL, 'Jaro 2024', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1', '2024-03-21 00:00:00.000000', '2024-06-21 00:00:00.000000', 'FFFF00'),
    (NULL, 'Léto 2024', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1', '2024-06-21 00:00:00.000000', '2024-09-21 00:00:00.000000', '00FF00'),
    (NULL,'Podzim 2024',CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1', '2024-09-21 00:00:00.000000', '2024-12-21 00:00:00.000000', 'E36414'),
    (NULL, 'Zima 2025', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1', '2024-12-21 00:00:00.000000', '2025-03-21 00:00:00.000000', 'B3DAF1');

INSERT INTO `sale_tag` (`id`, `sale_id`, `tag_id`) VALUES
    (NULL, '1', '1'), (NULL, '1', '2'),
    (NULL, '2', '2'), (NULL, '2', '3'),
    (NULL, '3', '3'), (NULL, '3', '4'),
    (NULL, '4', '4'), (NULL, '4', '5'),
    (NULL, '5', '1'), (NULL, '5', '4');