CREATE DATABASE IF NOT EXISTS gampola_daytrip
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gampola_daytrip;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS places (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL UNIQUE,
  short_description VARCHAR(260) NOT NULL,
  description TEXT NOT NULL,
  distance_km DECIMAL(5,1) NOT NULL,
  latitude DECIMAL(10,7) NOT NULL,
  longitude DECIMAL(10,7) NOT NULL,
  opening_info VARCHAR(180) NOT NULL DEFAULT 'Check locally before visiting',
  entrance_fee VARCHAR(120) NOT NULL DEFAULT 'Check locally',
  best_time VARCHAR(120) NOT NULL DEFAULT 'Daytime',
  visit_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  facilities VARCHAR(255) NOT NULL DEFAULT 'Information not confirmed',
  travel_tips TEXT NOT NULL,
  image_url VARCHAR(500) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_places_category
    FOREIGN KEY (category_id) REFERENCES categories(id),
  CONSTRAINT chk_distance
    CHECK (distance_km >= 0 AND distance_km <= 25),
  CONSTRAINT chk_latitude
    CHECK (latitude BETWEEN -90 AND 90),
  CONSTRAINT chk_longitude
    CHECK (longitude BETWEEN -180 AND 180)
);

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO categories (name)
VALUES
  ('Nature'),
  ('Religious'),
  ('Heritage'),
  ('Cultural'),
  ('Sightseeing');

INSERT IGNORE INTO places (
  category_id,
  name,
  slug,
  short_description,
  description,
  distance_km,
  latitude,
  longitude,
  opening_info,
  entrance_fee,
  best_time,
  visit_minutes,
  facilities,
  travel_tips,
  image_url
)
SELECT
  c.id,
  s.name,
  s.slug,
  s.short_description,
  s.description,
  s.distance_km,
  s.latitude,
  s.longitude,
  s.opening_info,
  s.entrance_fee,
  s.best_time,
  s.visit_minutes,
  s.facilities,
  s.travel_tips,
  s.image_url
FROM (
  SELECT
    'Nature' AS category,
    'Ambuluwawa Biodiversity Complex' AS name,
    'ambuluwawa' AS slug,
    'A hilltop biodiversity complex with a distinctive tower and wide views.' AS short_description,
    'Visit the hilltop complex for panoramic views over the surrounding highlands. The steep, narrow tower is an optional climb; take care and follow site guidance.' AS description,
    5.0 AS distance_km,
    7.1539 AS latitude,
    80.5685 AS longitude,
    'Daytime; confirm current access before travel' AS opening_info,
    'Check locally' AS entrance_fee,
    'Early morning for clearer views' AS best_time,
    120 AS visit_minutes,
    'Parking; facilities may vary' AS facilities,
    'Wear comfortable shoes, bring water, and check weather and access conditions before setting out.' AS travel_tips,
    '/gampola-daytrip/assets/images/ambuluwawa.jpg' AS image_url

  UNION ALL SELECT
    'Religious',
    'Ambekke Devalaya',
    'ambekke-devalaya',
    'A historic shrine known for finely carved wooden pillars.',
    'A celebrated place of worship and heritage, noted for its detailed timber carvings. Dress respectfully and ask before photographing people or ceremonies.',
    6.0,
    7.2140,
    80.5744,
    'Daytime; confirm locally',
    'Check locally',
    'Morning',
    60,
    'Local parking; facilities not confirmed',
    'Dress modestly and remove footwear where requested.',
    '/gampola-daytrip/assets/images/ambekke.jpg'

  UNION ALL SELECT
    'Religious',
    'Lankatilaka Raja Maha Viharaya',
    'lankatilaka-viharaya',
    'A hilltop temple with historic architecture and valley views.',
    'Explore the historic temple complex and its architectural details. Visitors should be mindful of religious observances and local etiquette.',
    8.0,
    7.2586,
    80.5728,
    'Daytime; confirm locally',
    'Check locally',
    'Morning',
    60,
    'Facilities not confirmed',
    'Wear modest clothing; be prepared for steps and uneven surfaces.',
    '/gampola-daytrip/assets/images/lankatilaka.jpg'

  UNION ALL SELECT
    'Religious',
    'Gadaladeniya Temple',
    'gadaladeniya-temple',
    'A historic stone temple in the Gampola region.',
    'A Buddhist temple associated with the Gampola period, offering a quiet stop for visitors interested in local history and architecture.',
    11.0,
    7.2597,
    80.5700,
    'Daytime; confirm locally',
    'Check locally',
    'Morning',
    45,
    'Facilities not confirmed',
    'Respect worshippers and check access arrangements locally.',
    '/gampola-daytrip/assets/images/gadaladeniya.jpg'

  UNION ALL SELECT
    'Nature',
    'Royal Botanic Gardens, Peradeniya',
    'peradeniya-gardens',
    'A celebrated botanical garden beside the Mahaweli River.',
    'Spend time among the garden collections, tree avenues and landscaped grounds. The site is large, so allow enough time for walking.',
    12.0,
    7.2720,
    80.5950,
    'Check official visitor information',
    'Check locally',
    'Morning',
    180,
    'Visitor facilities available; confirm current services',
    'Wear walking shoes, carry water and allow several hours for a relaxed visit.',
    '/gampola-daytrip/assets/images/peradeniya.jpg'

  UNION ALL SELECT
    'Cultural',
    'Hanthana Tea Museum',
    'hanthana-tea-museum',
    'A museum exploring Sri Lanka’s tea heritage.',
    'Learn about the history and production of Ceylon tea at this museum in the Hanthana hills. Confirm operating days and access before visiting.',
    13.0,
    7.2640,
    80.6240,
    'Confirm operating days before travel',
    'Check locally',
    'Late morning',
    90,
    'Facilities may vary',
    'Check opening status in advance; the approach includes hillside roads.',
    '/gampola-daytrip/assets/images/hanthana-tea-museum.jpg'

  UNION ALL SELECT
    'Religious',
    'Temple of the Sacred Tooth Relic',
    'temple-of-the-tooth',
    'A major Buddhist pilgrimage site in central Kandy.',
    'Visit the revered temple complex in Kandy. Visitors should allow time for security and observe local customs and dress guidance.',
    16.0,
    7.2936,
    80.6413,
    'Check official visitor information',
    'Check locally',
    'Morning or evening puja',
    90,
    'Facilities in the surrounding precinct',
    'Dress respectfully and expect crowds during religious ceremonies.',
    '/gampola-daytrip/assets/images/temple-of-tooth.jpg'

  UNION ALL SELECT
    'Nature',
    'Kandy Lake',
    'kandy-lake',
    'A scenic lakeside walk in the heart of Kandy.',
    'Enjoy a stroll beside the historic lake near central Kandy. Follow posted rules and use care around traffic when accessing the promenade.',
    16.0,
    7.2936,
    80.6400,
    'Public area; check local conditions',
    'No fee known; verify locally',
    'Early morning or late afternoon',
    60,
    'Town facilities nearby',
    'Allow for town traffic and keep to pedestrian areas.',
    '/gampola-daytrip/assets/images/kandy-lake.jpg'

  UNION ALL SELECT
    'Cultural',
    'Kandy National Museum',
    'kandy-national-museum',
    'Museum collections documenting Kandyan history and culture.',
    'Explore objects and displays connected with the Kandyan Kingdom and Sri Lankan history. Confirm opening hours and current exhibitions in advance.',
    16.0,
    7.2920,
    80.6410,
    'Confirm opening hours before travel',
    'Check locally',
    'Late morning',
    90,
    'Town facilities nearby',
    'Check the latest visitor information before making the trip.',
    '/gampola-daytrip/assets/images/kandy-museum.jpg'

  UNION ALL SELECT
    'Nature',
    'Udawattakele Forest Reserve',
    'udawattakele-forest',
    'A forest reserve offering shaded walking trails above Kandy.',
    'A green escape near the city with walking trails and opportunities to observe local nature. Stay on marked routes and respect wildlife.',
    17.0,
    7.3004,
    80.6400,
    'Confirm entry hours locally',
    'Check locally',
    'Early morning',
    120,
    'Facilities limited; verify locally',
    'Wear suitable shoes, carry water and avoid feeding wildlife.',
    '/gampola-daytrip/assets/images/udawattakele.jpg'

  UNION ALL SELECT
    'Nature',
    'Kotmale Dam',
    'kotmale-dam',
    'A large dam and reservoir in the Kotmale area.',
    'Visit the Kotmale Dam area for views of the dam and surrounding highlands. Confirm public access and local conditions before travelling.',
    14.0,
    7.06083,
    80.59722,
    'Confirm public access before travel',
    'Check locally',
    'Daytime',
    90,
    'Facilities may be limited; verify locally',
    'Check the route and access conditions before setting out.',
    '/gampola-daytrip/assets/images/kothmale.jpeg'

  UNION ALL SELECT
    'Sightseeing',
    'Gampola Town',
    'gampola-town',
    'A local starting point for exploring the surrounding area.',
    'Use Gampola as the starting point for a day around nearby temples, countryside and hilltop viewpoints. This is a planning anchor rather than a ticketed attraction.',
    0.0,
    7.1640,
    80.5760,
    'Public town centre',
    'No fee',
    'Daytime',
    45,
    'Shops and services in town',
    'Use the town as your starting point and confirm onward transport.',
    '/gampola-daytrip/assets/images/gampola-town.jpg'
) AS s
JOIN categories AS c ON c.name = s.category;

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/ambuluwawa.jpeg'
WHERE slug = 'ambuluwawa';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/ambekke.jpeg'
WHERE slug = 'ambekke-devalaya';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/lankathilaka.jpeg'
WHERE slug = 'lankatilaka-viharaya';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/gadaladeniya.jpeg'
WHERE slug = 'gadaladeniya-temple';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/peradeniya.jpeg'
WHERE slug = 'peradeniya-gardens';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/hanthana-tea-museum.jpeg'
WHERE slug = 'hanthana-tea-museum';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/temple-of-the-tooth.jpeg'
WHERE slug = 'temple-of-the-tooth';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/kandy-lake.jpeg'
WHERE slug = 'kandy-lake';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/kandy-museum.jpg'
WHERE slug = 'kandy-national-museum';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/udawattakele.jpeg'
WHERE slug = 'udawattakele-forest';

UPDATE places
SET image_url = '/gampola-daytrip/assets/images/gampola-town.jpg'
WHERE slug = 'gampola-town';

-- Correct the existing Kotmale record too.
-- This handles a row that was previously inserted with old heritage-trail details.
UPDATE places
SET
  category_id = (SELECT id FROM categories WHERE name = 'Nature' LIMIT 1),
  name = 'Kotmale Dam',
  slug = 'kotmale-dam',
  short_description = 'A large dam and reservoir in the Kotmale area.',
  description = 'Visit the Kotmale Dam area for views of the dam and surrounding highlands. Confirm public access and local conditions before travelling.',
  distance_km = 14.0,
  latitude = 7.06083,
  longitude = 80.59722,
  opening_info = 'Confirm public access before travel',
  entrance_fee = 'Check locally',
  best_time = 'Daytime',
  visit_minutes = 90,
  facilities = 'Facilities may be limited; verify locally',
  travel_tips = 'Check the route and access conditions before setting out.',
  image_url = '/gampola-daytrip/assets/images/kothmale.jpg'
WHERE slug = 'kotmale-dam';

-- Check the saved Kotmale Dam record.
SELECT id, name, slug, distance_km, image_url
FROM places
WHERE slug = 'kotmale-dam';

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);