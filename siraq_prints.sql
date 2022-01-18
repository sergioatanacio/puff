
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name_user` varchar(300) NOT NULL,
  `email` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `description` text NOT NULL,
  `phone` varchar(300) NOT NULL,
  `direction` text NOT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `tags` (
  `id_tag` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name_tag` varchar(300) NOT NULL,
  `description_tag` text NOT NULL,
  `cover_tag` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `sheets` (
  `id_sheets` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name_sheets` varchar(300) NOT NULL,
  `description_sheets` text NOT NULL,
  `price` int NOT NULL,
  `amount` int NOT NULL,
  `image_sheets` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `relations` (
  `id_relations` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `id_tag` int NOT NULL,
  `id_sheets` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` 
  (`id_user`, `name_user`, `email`, `password`, `description`, `phone`, `direction`, `image`) 
VALUES
  (1, 'andres', 'andres@gmail.com', 'andresc', 'Entusiasta', '978654321', 'La Molina', 'img'),
  (2, 'julio', 'julio@gmail.com', 'julioc', 'Entusiasta', '978654321', 'Barranco', 'img');




INSERT INTO `tags` 
  (`id_tag`, `name_tag`, `description_tag`, `cover_tag`)
VALUES
  (1, 'Navideños', 'Todo sobre navidades', 'Link de imagen del tag.'),
  (2, 'Parejas', 'Sobre todo viniles para poleras', 'Link de imagen del tag.'),
  (3, 'Cumpleaños', 'Para niños', 'Link de imagen del tag.');




INSERT INTO `sheets` 
  (`id_sheets`, `name_sheets`, `description_sheets`, `price`, `amount`, `image_sheets`)
VALUES
  (1, 'Mikey mouse', 'Muñeco de disney', '2', '200', 'Link de Mikey mouse'),
  (2, 'Cobra Kai', 'Arbol insignia', '3', '30', 'Link de Cobra Kai'),
  (3, 'Grut', 'De marvel', '5', '50', 'Link de Grut');


