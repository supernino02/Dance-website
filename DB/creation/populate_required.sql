USE S5175710;
START TRANSACTION;
/*NON SERVE, ci sono già valori*/

INSERT INTO `users` (`name`, `surname`, `email`, `phone_number`, `password`, `fiscal_code`,`role`) VALUES
('a', 'a', 'aa@aa.aa', '0102030405', '$2y$10$m1adbCZTdYPNzyaMbHlHHuoZv4ks5HHkJP.kJPEj.PlIAqvfVz8aW', 'AAAAAA00A00A000A', 'admin'),
('b', 'b', 'bb@bb.bb', '0000000000', '$2y$10$ikEFHr8DgAmgDQcEJdRtLOWqL3hOVT3hn6rU22LTc6Ze3Ze4/2Mz.', null, 'user'),
('c', 'c', 'cc@cc.cc', null , '$2y$10$bPdlz7A4zsed1jy7YalYnuH78qca3wdqPbVNEGCoq.Hh/iEa8P4s6', 'CCCCCC00C00C000C', 'user');

INSERT INTO `dance_disciplines`(`type`) VALUES 
    ('bachata'),
    ('bachata sensual'),
    ('salsa'),
    ('miscellaneous'),
    ('lady style');

INSERT INTO `levels`(`name`) VALUES 
    ('principiante'),
    ('intermedio'),
    ('avanzato'),
    ('master-class');

INSERT INTO `product_types`(`type`,'icon_path','description') VALUES 
    ('online','icona_1','<p>Che tu voglia migliorare la tua tecnica o imparare nuovi stili,i nostri <b>corsi online</b> ti permetteranno di farlo comodamente da casa.</p>'),
    ('collettivo','icona_2','<p>Vieni a conoscere nuove persone e scoprire il piacere di ballare nei <b>corsi collettivi</b>, lasciati trasportare dalla musica!</p>'),
    ('privato','icona_3',"<p>Desideri un'attenzione personalizzata per perfezionare il tuo stile? Le <b>lezioni private</b> sono la soluzione ideale per te.</p>"),
    ('evento','icona_4',"<p>Scopri l'arte della coreografia con i nostri corsi specializzati! Questi gruppi <b>Coreografici</b> sono ideali per chi desidera imparare a creare e interpretare coreografie uniche e coinvolgenti.</p>");

INSERT INTO `products`(`id_product`, `name`,    `poster_path`, `description`,                      `level`,        `type`, `dance_discipline`, `total_price`, `discount`, `expiration_date`) VALUES 
    (1,'corso online bachata principiante',  NULL, 'corso pazzo',                      'principiante', 'online',     'bachata',           100,           10,        NULL),
    (2,'corso online bachata intermedio',    NULL, 'corso ancora piu pazzo',           'intermedio',   'online',     'bachata',           150,           0,         NULL),
    (3,'lezione privata salsa avanzato',     NULL, 'costicchia',                       'avanzato',     'privato',    'salsa',             25,            0,         NULL),
    (4,'evento gratis',                      NULL, 'solo per i pro',                   'master-class', 'evento',     'lady style',        0,             0,         '2028-02-06'),
    (5,'evento scaduto',                     NULL, 'solo per i pro, ma è scaduto',     'master-class', 'evento',     'lady style',        0,             0,         '2020-02-06'),
    (6,'corso con file','corso di esempio/vetrina/poster.png',     'contiene dei file','principiante', 'online',     'bachata',           150,           75,        NULL);


INSERT INTO `products_in_carts`(`user`, `id_product`, `quantity`) VALUES 
    ('bb@bb.bb',1,10),
    ('bb@bb.bb',2,1),
    ('bb@bb.bb',3,5);

INSERT INTO `purchasable_files`(`id_product`,`path`,`description`) VALUES
    (6,'corso di esempio/esibizione.MOV','Un esempio di esibizione di ballo'),
    (6,'corso di esempio/cursed.mp4','Un video fatto da AI'),
    (6,'corso di esempio/esempio.pdf','Un file pdf di appunti di calculus');

INSERT INTO `public_files`(`id_product`,`path`,`description`) VALUES
    (6,'corso di esempio/vetrina/presentazione.mp4','Un file pdf di appunti di calculus');

COMMIT;