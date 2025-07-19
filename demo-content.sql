-- Demo content for Seyahat Marketplace
-- Sample categories and destinations

INSERT INTO wp_terms (term_id, name, slug, term_group) VALUES
(1, 'Şehir Turları', 'sehir-turlari', 0),
(2, 'Müze & Sanat', 'muze-sanat', 0),
(3, 'Macera Sporları', 'macera-sporlari', 0),
(4, 'Yemek Turları', 'yemek-turlari', 0),
(5, 'Gece Hayatı', 'gece-hayati', 0),
(6, 'İstanbul', 'istanbul', 0),
(7, 'Ankara', 'ankara', 0),
(8, 'İzmir', 'izmir', 0),
(9, 'Antalya', 'antalya', 0),
(10, 'Kapadokya', 'kapadokya', 0);

INSERT INTO wp_term_taxonomy (term_taxonomy_id, term_id, taxonomy, description, parent, count) VALUES
(1, 1, 'tour_category', 'Şehir gezileri ve kent turları', 0, 0),
(2, 2, 'tour_category', 'Müze ziyaretleri ve sanat etkinlikleri', 0, 0),
(3, 3, 'tour_category', 'Ekstrem sporlar ve macera aktiviteleri', 0, 0),
(4, 4, 'tour_category', 'Gastronomi turları ve yemek deneyimleri', 0, 0),
(5, 5, 'tour_category', 'Gece kulüpleri ve eğlence mekanları', 0, 0),
(6, 6, 'destination', 'Türkiyenin en büyük şehri', 0, 0),
(7, 7, 'destination', 'Türkiyenin başkenti', 0, 0),
(8, 8, 'destination', 'Ege kıyısının incisi', 0, 0),
(9, 9, 'destination', 'Akdeniz kıyısının turizm merkezi', 0, 0),
(10, 10, 'destination', 'Peri bacaları diyarı', 0, 0);

-- Sample vendor data
INSERT INTO wp_posts (post_title, post_content, post_status, post_type, post_author, post_date) VALUES
('İstanbul Tur Rehberi A.Ş.', 'Profesyonel tur hizmetleri sunan deneyimli şirket', 'publish', 'seyahat_vendor', 1, NOW()),
('Kapadokya Macera Turları', 'Kapadokya bölgesinde macera turları düzenleyen uzman ekip', 'publish', 'seyahat_vendor', 1, NOW()),
('Antalya Deniz Sporları', 'Su sporları ve deniz aktiviteleri konusunda uzman', 'publish', 'seyahat_vendor', 1, NOW());

-- Sample tour data
INSERT INTO wp_posts (post_title, post_content, post_excerpt, post_status, post_type, post_author, post_date) VALUES
('Boğaz Turu ve Dolmabahçe Sarayı', 'İstanbul Boğazının eşsiz güzelliklerini keşfedin ve Dolmabahçe Sarayının tarihi atmosferinde yolculuk yapın.', 'İstanbul Boğazı ve Dolmabahçe Sarayı turu', 'publish', 'seyahat_tour', 1, NOW()),
('Kapadokya Balon Turu', 'Kapadokyanın büyülü peyzajını sıcak hava balonu ile gökyüzünden deneyimleyin.', 'Kapadokya sıcak hava balonu deneyimi', 'publish', 'seyahat_tour', 1, NOW()),
('Antalya Rafting Macerası', 'Köprüçay Nehrinde adrenalin dolu rafting deneyimi yaşayın.', 'Köprüçay rafting turu', 'publish', 'seyahat_tour', 1, NOW()),
('İstanbul Gastronomi Turu', 'İstanbulun en lezzetli sokak yemeklerini tadın ve yerel kültürü keşfedin.', 'İstanbul yemek turu', 'publish', 'seyahat_tour', 1, NOW()),
('Ayasofya ve Sultanahmet Turu', 'İstanbulun tarihi kalbi Sultanahmet bölgesini rehber eşliğinde gezin.', 'Sultanahmet tarihi tur', 'publish', 'seyahat_tour', 1, NOW());
