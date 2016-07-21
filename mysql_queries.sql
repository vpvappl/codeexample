-- ссылка назад
SELECT 
    wp1.post_title,
    wp2.post_name
FROM wp_posts as wp1
LEFT JOIN wp_posts as wp2 
WHERE wp1.post_status = 'publish'
    AND wp2.post_name = 'referendum-v-velikobritanii-kak-primer-samonatsiestroitelstva'
    AND wp1.post_date < wp2.post_date
ORDER BY wp1.post_date
DESC LIMIT 1

-- выбрать страницы родители
SELECT
COUNT(wp1.post_parent) as cou,
wp2.ID,
wp2.post_name,
wp2.post_title
FROM wp_posts as wp1
LEFT JOIN wp_posts as wp2 ON wp1.post_parent = wp2.ID
WHERE wp1.post_parent != 0 
    AND wp1.post_status = 'publish' 
    AND wp1.post_type = 'page'
GROUP BY wp1.post_parent
HAVING cou > 1
ORDER BY cou DESC

-- выборка данных по статье
-- рубрика, дата создания, автор
-- ogimg, from_here, комментарии дискус, комментарии facebook
-- wp_term_relationships = object_id : term_taxonomy_id
-- wp_postmeta = post_id : meta_key, meta_value
SELECT
    wp_posts.ID,
    wp_posts.post_title,
    wp_posts.post_name,
    wp_postmeta.meta_key,
    wp_postmeta.meta_value,
    wp_terms.name,
    wp_terms.slug
FROM wp_posts
LEFT JOIN wp_postmeta ON wp_postmeta.post_id = wp_posts.ID
LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
LEFT JOIN wp_term_taxonomy USING (term_taxonomy_id)
LEFT JOIN wp_terms USING (term_id)
WHERE wp_posts.post_name = 'bogataya-nischaya-rossiya'
    AND wp_postmeta.meta_key IN ('ogimg','from_here')
    AND wp_term_taxonomy.taxonomy = 'category'
    
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

-- вычислить пересечение таблиц
SELECT
    `genres`.`name`,
    `genres`.`id_gen` as 'gid_gen',
    `books`.`id_gen` as 'bid_gen',
    `books`.`id_off`
FROM `genres`
LEFT JOIN `books` USING (`id_gen`)
WHERE `books`.`id_gen` IS NULL;

SELECT
    `authors`.`name`,
    `authors`.`id_auth` as 'aid_auth',
    `books`.`id_auth` as 'bid_auth',
    `books`.`id_off`
FROM `authors`
LEFT JOIN `books` USING (`id_auth`)
WHERE `books`.`id_auth` IS NULL;

SELECT
    `publishers`.`name`,
    `publishers`.`id_pub` as 'pid_pub',
    `books`.`id_pub` as 'bid_pub',
    `books`.`id_off`
FROM `publishers`
LEFT JOIN `books` USING (`id_pub`)
WHERE `books`.`id_pub` IS NULL;

SELECT
    `series`.`name`,
    `series`.`id_ser` as 'sid_ser',
    `books`.`id_ser` as 'bid_ser',
    `books`.`id_off`
FROM `series`
LEFT JOIN `books` USING (`id_ser`)
WHERE `books`.`id_ser` IS NULL;
-- ----------------------------------------------------------------------------

-- получить книги поступившие сегодня
SELECT
    `books`.`id_off`,
    `books`.`name`,
    `books`.`picture`
FROM `books`
WHERE DATE(`dt`) = DATE(CURDATE());

-- подсчитать количество книг утром
SELECT
    COUNT(`id_off`) as 'count'
FROM `books`
WHERE DATE(`dt`) = DATE(CURDATE());

-- получить популярных авторов
-- отсортировав их по фамилиям
SELECT 
    `id_auth`,
    `name`,
    SUBSTRING_INDEX(`name`, ' ', -1) AS 'name_last'
FROM `authors` 
WHERE `name` IN ('Захар Прилепин','Кассандра Клэр','Марина и Сергей Дяченко','Борис Акунин','Роман Злотников','Макс Фрай','Ю Несбё','Наринэ Абгарян','Стиг Ларссон','Юлия Гиппенрейтер')
ORDER BY `name_last`

-- получить всех русскофамильных авторов
SELECT 
`name`,
SUBSTRING_INDEX(`name`, ' ', -1) AS 'name_last' 
FROM `authors`
WHERE `name` NOT LIKE '%(%' AND `name` NOT RLIKE '[a-zA-Z]'
ORDER BY `name_last`

SELECT 
`name`,
SUBSTRING_INDEX(`name`, ' ', -1) AS 'name_last' 
FROM `authors`
WHERE `name` NOT LIKE '%(%' AND `name` NOT RLIKE '[0-9,\s,a-zA-Z,(,«]'
ORDER BY `name_last`


-- получить последнее слово из поля
-- получить фамилии и отсортировать по алфавиту
-- где фамилии не имеют псевдонимов
SELECT 
`name`,
SUBSTRING_INDEX(`name`, ' ', -1) AS 'name_last' 
FROM `authors`
WHERE `name` NOT LIKE '%(%'
ORDER BY `name_last`

-- выбрать всех авторов 
-- у кого не английская фамилия
SELECT * FROM `authors` WHERE `name` NOT RLIKE '[a-zA-Z]'

-- получить авторов с творческими псевдонимами
-- отделив псевдоним от оригинального имени
SELECT 
`name`,
SUBSTRING_INDEX(`name`, ' ', -1) AS 'name_last' 
FROM `authors`
WHERE `name` LIKE '%(%)%'
ORDER BY `name_last`

-- выборка авторов по начальной букве в фамилии
-- без псевдонимов
SELECT * FROM `authors` WHERE `name` LIKE '% и%'

SELECT 
`name`,
SUBSTRING_INDEX(`name`, ' ', -1) AS 'name_last' 
FROM `authors`
WHERE `name` NOT LIKE '%(%'
GROUP BY `name`
HAVING SELECT LOCATE('и', 'foobar') 
ORDER BY `name`

-- получение книг с учетом их типа ---------------------------------------------
SELECT 
    `books`.`id_off`,
    `books`.`name`,
    `books`.`picture`
FROM `books`
LEFT JOIN `types` USING(`id_type`)
WHERE `types`.`type` = 'book'


-- одна книга
-- авторы с подсчетом книг
-- список серий с к-вом книг
-- список жанров основных с к-вом книг
-- список жанров внутри основного с к-вом книг
-- список книг внутри жанра основного
-- список книг внутри жанра дочернего
-- список книг внутри серии
-- список серий с коилчеством книг




-- список жанров основных и вложенных по названиям -----------------------------
SELECT 
    `gen2`.`name` AS 'parent_name',
    `gen1`.`id_parent` AS 'parent_id',
    `gen1`.`id_gen` AS 'child_id',
    `gen1`.`name` AS 'child_name'
FROM `genres` AS `gen1`
JOIN `genres` AS `gen2` ON `gen1`.`id_parent` = `gen2`.`id_gen`
WHERE `gen1`.`id_gen` IN(
    SELECT DISTINCT 
        `books`.`id_gen`
    FROM `books`
)
ORDER BY `gen1`.`id_parent`,`gen1`.`id_gen`;


-- !!! список жанров основных с к-вом книг ----------------------------------------
SELECT 
    COUNT(`books`.`id_gen`) AS 'cou',
    `gen1`.`id_parent` AS 'id_par',
    `gen2`.`name` AS 'name_parent_genre'
FROM `books`
JOIN `genres` AS `gen1` USING(`id_gen`)
JOIN `genres` AS `gen2` ON `gen1`.`id_parent` = `gen2`.`id_gen`
GROUP BY `id_par`
ORDER BY `id_par`;

-- !!! список жанров внутри основного с к-вом книг --------------------------------
SELECT 
    COUNT(`books`.`id_gen`) AS 'cou',
    `gen1`.`id_gen`,
    `gen1`.`id_parent` AS 'id_par',
    `gen1`.`name`,
    `gen2`.`name` AS 'name_parent_genre'
FROM `books`
JOIN `genres` AS `gen1` USING(`id_gen`)
LEFT JOIN `genres` AS `gen2` ON `gen1`.`id_parent` = `gen2`.`id_gen`
WHERE `gen1`.`id_parent` = 10003
GROUP BY `gen1`.`id_gen`
ORDER BY `cou` DESC;



-- !!! список книг внутри жанра основного -----------------------------------------
SELECT 
    `books`.`id_off`,
    `books`.`name` AS 'name_book',
    `gen1`.`id_gen`,
    `gen1`.`name` AS 'name_genre',
    `gen2`.`name` AS 'name_parent_genre'
FROM `books`
JOIN `genres` AS `gen1` USING(`id_gen`)
LEFT JOIN `genres` AS `gen2` ON `gen1`.`id_parent` = `gen2`.`id_gen`
WHERE `gen1`.`id_parent` = 10003
ORDER BY `books`.`name`;


-- !!! список книг внутри жанра дочернего -----------------------------------------
SELECT 
    `books`.`id_off`,
    `books`.`name` AS 'name_book',
    `gen1`.`id_gen`,
    `gen1`.`name` AS 'name_genre'
FROM `books`
JOIN `genres` AS `gen1` USING(`id_gen`)
WHERE `gen1`.`id_gen` = 4000
ORDER BY `books`.`name`;


-- !!! список книг внутри серии ---------------------------------------------------
SELECT 
    `books`.`id_off`,
    `books`.`name` AS 'name_book',
    `ser1`.`id_ser`,
    `ser1`.`name` AS 'name_serie'
FROM `books`
JOIN `series` AS `ser1` USING(`id_ser`)
WHERE `ser1`.`id_ser` = 482
ORDER BY `books`.`name`;


-- !!! список серий с количеством книг --------------------------------------------
SELECT 
    COUNT(`books`.`id_gen`) AS 'cou',
    `ser1`.`id_ser`,
    `ser1`.`name`
FROM `books`
JOIN `series` AS `ser1` USING(`id_ser`)
GROUP BY `id_ser`
ORDER BY `cou` DESC;

-- !!! авторы с подсчетом книг ----------------------------------------------------
SELECT 
    COUNT(`books`.`id_gen`) AS 'cou',
    `authors`.`id_auth`,
    `authors`.`name`
FROM `books`
JOIN `authors` USING(`id_auth`)
GROUP BY `authors`.`name`
ORDER BY `cou` DESC;


-- !!! одна книга -----------------------------------------------------------------
-- 1 название книги
-- 2 автор книги
-- 3 серия книги
-- 4 жанр книги
-- 5 цена книги
-- 6 год издания
-- 7 ссылка на книгу
-- 8 картинка
-- 9 издательство
-- 10 описание

SELECT
    `partner`.`refid` AS 'refid',
    `books`.`id_off`,
    `books`.`name`,
    `books`.`description`,
    CONCAT(`books`.`url`,'?lfrom=',`refid`) AS 'url',
    `books`.`picture`,
    `books`.`price`,
    `books`.`year`,
    `authors`.`name` AS 'author',
    `genres`.`name` AS 'genre',
    `series`.`name` AS 'serie',
    `publishers`.`name` AS 'publisher'
FROM `books`
LEFT JOIN `authors` USING(`id_auth`)
LEFT JOIN `genres` USING(`id_gen`)
LEFT JOIN `series` USING(`id_ser`) 
LEFT JOIN `publishers` USING(`id_pub`) 
JOIN `partner`
WHERE `id_off` = 118363;
