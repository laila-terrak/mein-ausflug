CREATE DATABASE IF NOT EXISTS mein_ausflug;
USE mein_ausflug;

CREATE TABLE IF NOT EXISTS destinations (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(40),
    description VARCHAR(250),
    text VARCHAR(1000)
);

CREATE TABLE IF NOT EXISTS hotels (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(40),
    description VARCHAR(250),
    text VARCHAR(1000),
    available_rooms INT,
    amenities VARCHAR(40),
    room_number INT,
    destination_id CHAR(36),
    FOREIGN KEY (destination_id) REFERENCES destinations(id)
);

CREATE TABLE IF NOT EXISTS amenities (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(40),
    hotel_id CHAR(36),
    FOREIGN KEY (hotel_id) REFERENCES hotels(id)
);

CREATE TABLE IF NOT EXISTS images (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    url VARCHAR(255),
    hotel_id CHAR(36) NULL,
    destination_id CHAR(36) NULL,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    CHECK (hotel_id IS NOT NULL OR destination_id IS NOT NULL)
);

-- Insert sample data into destinations
INSERT INTO destinations (id, name, description, text) VALUES
('11111111-1111-1111-1111-111111111111', 'Paris', 'The city of lights, known for art, fashion, gastronomy and culture.', 'Welcome to Paris, a city teeming with famous landmarks, exquisite cuisine, and a vibrant art scene. Explore the Eiffel Tower, the Louvre, and Notre-Dame Cathedral. Indulge in the city’s unparalleled culinary delights and immerse yourself in its rich history and romantic charm.'),
('22222222-2222-2222-2222-222222222222', 'New York', 'The Big Apple, known for iconic skyscrapers, world-class museums, and diverse neighborhoods.', 'Discover New York, a city that never sleeps, offering a dazzling array of attractions. From the Statue of Liberty to the neon lights of Times Square, this metropolis pulses with energy, culture, and innovation. Enjoy Broadway shows, explore Central Park, and experience the city’s global culinary scene.'),
('33333333-3333-3333-3333-333333333333', 'Tokyo', 'A bustling metropolis blending tradition and modernity.', 'Experience Tokyo, where ancient temples coexist with towering skyscrapers. Explore the vibrant districts of Shibuya and Shinjuku, savor authentic sushi, and partake in centuries-old tea ceremonies. Tokyo’s unique fusion of technology, fashion, and serene gardens invites you into a world of contrast and discovery.'),
('44444444-4444-4444-4444-444444444444', 'Sydney', 'A coastal city famed for its Opera House and beautiful beaches.', 'Dive into Sydney’s laid-back vibe, from the iconic Opera House to Bondi Beach. Enjoy fresh seafood, explore the harbor, and immerse yourself in Australia’s rich cultural tapestry. Sydney’s blend of natural wonders, vibrant arts, and multicultural cuisine promises an unforgettable journey.');

-- Insert sample data into hotels
INSERT INTO hotels (id, name, description, text, available_rooms, amenities, room_number, destination_id) VALUES
('aaaaaaa1-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'Hôtel Lumière', 'A boutique hotel in the heart of Paris.', 'Hôtel Lumière offers elegantly furnished rooms, world-class dining, and personalized service. Just steps from the Louvre, it’s the perfect home base for exploring the city’s cultural treasures.', 20, 'Wi-Fi, Spa', 101, '11111111-1111-1111-1111-111111111111'),
('aaaaaaa2-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'Eiffel Retreat', 'Cozy rooms near the Eiffel Tower.', 'Eiffel Retreat features contemporary interiors, plush bedding, and a rooftop terrace with a view of the Tower. Indulge in a continental breakfast and explore the nearby charming cafes and boutiques.', 15, 'Free Breakfast, Wi-Fi', 201, '11111111-1111-1111-1111-111111111111'),

('bbbbbbb1-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'Times Square Inn', 'A vibrant hotel in Midtown Manhattan.', 'Times Square Inn boasts modern rooms, a lively bar, and immediate access to Broadway shows and top dining. Experience the city’s energy right at your doorstep.', 30, 'Wi-Fi, Gym', 301, '22222222-2222-2222-2222-222222222222'),
('bbbbbbb2-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'Central Park Suites', 'Luxury suites steps away from Central Park.', 'Central Park Suites offers spacious rooms, panoramic views, and a serene lounge. Start your day with a jog in the park and enjoy world-class museums and dining just minutes away.', 25, 'Pool, Wi-Fi', 401, '22222222-2222-2222-2222-222222222222'),

('ccccccc1-cccc-cccc-cccc-cccccccccccc', 'Shibuya Bliss', 'A stylish hotel in Tokyo’s fashion district.', 'Shibuya Bliss blends modern design with traditional Japanese hospitality. Savor sushi at our in-house restaurant and explore the bright lights and trend-setting shops of Shibuya.', 18, 'Wi-Fi, Onsen', 501, '33333333-3333-3333-3333-333333333333'),
('ccccccc2-cccc-cccc-cccc-cccccccccccc', 'Zen Garden Inn', 'A tranquil escape in central Tokyo.', 'Zen Garden Inn offers minimalist décor, a serene tea room, and a private garden. Experience the calm side of Tokyo while remaining close to major attractions.', 12, 'Wi-Fi, Tea Ceremony', 601, '33333333-3333-3333-3333-333333333333'),

('ddddddd1-dddd-dddd-dddd-dddddddddddd', 'Harbor View Lodge', 'Waterfront hotel with Opera House views.', 'Harbor View Lodge provides spacious rooms and a waterfront restaurant serving fresh seafood. Enjoy the iconic skyline and stroll along the harbor.', 22, 'Wi-Fi, Beach Access', 701, '44444444-4444-4444-4444-444444444444'),
('ddddddd2-dddd-dddd-dddd-dddddddddddd', 'Bondi Beach Retreat', 'Relaxed hotel near the surf.', 'Bondi Beach Retreat offers airy rooms and a laid-back lounge. Enjoy the coastal breeze, surf lessons, and Sydney’s renowned café culture.', 10, 'Wi-Fi, Surf Rentals', 801, '44444444-4444-4444-4444-444444444444');

-- Insert sample data into amenities
INSERT INTO amenities (id, name, hotel_id) VALUES
('eeeeeee1-eeee-eeee-eeee-eeeeeeeeeeee', 'Free High-Speed Wi-Fi', 'aaaaaaa1-aaaa-aaaa-aaaa-aaaaaaaaaaaa'),
('eeeeeee2-eeee-eeee-eeee-eeeeeeeeeeee', 'Spa and Wellness Center', 'aaaaaaa1-aaaa-aaaa-aaaa-aaaaaaaaaaaa'),
('eeeeeee3-eeee-eeee-eeee-eeeeeeeeeeee', 'Rooftop Terrace', 'aaaaaaa2-aaaa-aaaa-aaaa-aaaaaaaaaaaa'),

('eeeeeee4-eeee-eeee-eeee-eeeeeeeeeeee', 'In-Room Dining', 'bbbbbbb1-bbbb-bbbb-bbbb-bbbbbbbbbbbb'),
('eeeeeee5-eeee-eeee-eeee-eeeeeeeeeeee', '24/7 Fitness Center', 'bbbbbbb1-bbbb-bbbb-bbbb-bbbbbbbbbbbb'),
('eeeeeee6-eeee-eeee-eeee-eeeeeeeeeeee', 'Indoor Pool', 'bbbbbbb2-bbbb-bbbb-bbbb-bbbbbbbbbbbb'),

('eeeeeee7-eeee-eeee-eeee-eeeeeeeeeeee', 'Concierge Services', 'ccccccc1-cccc-cccc-cccc-cccccccccccc'),
('eeeeeee8-eeee-eeee-eeee-eeeeeeeeeeee', 'Traditional Tea Ceremony', 'ccccccc2-cccc-cccc-cccc-cccccccccccc'),

('eeeeeee9-eeee-eeee-eeee-eeeeeeeeeeee', 'Beach Towels & Chairs', 'ddddddd1-dddd-dddd-dddd-dddddddddddd'),
('ffffffff-ffff-ffff-ffff-ffffffffffff', 'Surfboard Rentals', 'ddddddd2-dddd-dddd-dddd-dddddddddddd');

-- Insert sample data into images
-- Destination images
INSERT INTO images (id, url, hotel_id, destination_id) VALUES
('ggggggg1-gggg-gggg-gggg-gggggggggggg', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, '11111111-1111-1111-1111-111111111111'),
('ggggggg2-gggg-gggg-gggg-gggggggggggg', 'https://images.unsplash.com/photo-1489348611450-4c0d746d949b?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, '11111111-1111-1111-1111-111111111111'),
('ggggggg3-gggg-gggg-gggg-gggggggggggg', 'https://images.unsplash.com/photo-1483653364400-eedcfb9f1f88?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, '22222222-2222-2222-2222-222222222222'),
('ggggggg4-gggg-gggg-gggg-gggggggggggg', 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?q=80&w=2998&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, '22222222-2222-2222-2222-222222222222'),
('ggggggg5-gggg-gggg-gggg-gggggggggggg', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?q=80&w=2988&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, '33333333-3333-3333-3333-333333333333'),
('ggggggg6-gggg-gggg-gggg-gggggggggggg', 'https://images.unsplash.com/photo-1508183723088-3fb747f85d81?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, '33333333-3333-3333-3333-333333333333'),
('ggggggg7-gggg-gggg-gggg-gggggggggggg', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, '44444444-4444-4444-4444-444444444444'),
('ggggggg8-gggg-gggg-gggg-gggggggggggg', 'https://images.unsplash.com/photo-1549202879-2051c839ac70?q=80&w=2874&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', NULL, '44444444-4444-4444-4444-444444444444'),
('hhhhhhh1-hhhh-hhhh-hhhh-hhhhhhhhhhhh', 'https://images.unsplash.com/photo-1609949851943-ff5336d1129f?q=80&w=2880&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'aaaaaaa1-aaaa-aaaa-aaaa-aaaaaaaaaaaa', NULL),
('hhhhhhh2-hhhh-hhhh-hhhh-hhhhhhhhhhhh', 'https://images.unsplash.com/photo-1687410833131-c608343acc8f?q=80&w=2831&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'aaaaaaa2-aaaa-aaaa-aaaa-aaaaaaaaaaaa', NULL),
('hhhhhhh3-hhhh-hhhh-hhhh-hhhhhhhhhhhh', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'bbbbbbb1-bbbb-bbbb-bbbb-bbbbbbbbbbbb', NULL),
('hhhhhhh4-hhhh-hhhh-hhhh-hhhhhhhhhhhh', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'bbbbbbb2-bbbb-bbbb-bbbb-bbbbbbbbbbbb', NULL),
('hhhhhhh5-hhhh-hhhh-hhhh-hhhhhhhhhhhh', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'ccccccc1-cccc-cccc-cccc-cccccccccccc', NULL),
('hhhhhhh6-hhhh-hhhh-hhhh-hhhhhhhhhhhh', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'ccccccc2-cccc-cccc-cccc-cccccccccccc', NULL),
('hhhhhhh7-hhhh-hhhh-hhhh-hhhhhhhhhhhh', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'ddddddd1-dddd-dddd-dddd-dddddddddddd', NULL),
('hhhhhhh8-hhhh-hhhh-hhhh-hhhhhhhhhhhh', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'ddddddd2-dddd-dddd-dddd-dddddddddddd', NULL);