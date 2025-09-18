-- Complete Image Update SQL Script
-- This script updates all hotels and room types with local images

-- Update hotels with local images
UPDATE hotels SET images = '["uploads/Hotels/Colombo/Hotel-1.png", "uploads/Hotels/Colombo/Hotel-2.png", "uploads/Hotels/Colombo/Hotel-3.png"]' WHERE city = 'Colombo';

UPDATE hotels SET images = '["uploads/Hotels/Ella/Hotel-1.png", "uploads/Hotels/Ella/Hotel-2.png"]' WHERE city = 'Ella';

UPDATE hotels SET images = '["uploads/Hotels/Matara/Matara-1.png", "uploads/Hotels/Matara/Matara-2.png"]' WHERE city = 'Matara';

UPDATE hotels SET images = '["uploads/Hotels/Nuwara Eliya/Hotel-1.png", "uploads/Hotels/Nuwara Eliya/Hotel-2.png", "uploads/Hotels/Nuwara Eliya/Hotel-3.png", "uploads/Hotels/Nuwara Eliya/Hotel-4.png"]' WHERE city = 'Nuwara Eliya';

-- Update room types with local images
-- Colombo rooms (including new Room-3.png)
UPDATE room_types SET images = '["uploads/Hotels/Colombo/Room-1.png"]' WHERE hotel_id = 1 AND type_name = 'Standard Room';
UPDATE room_types SET images = '["uploads/Hotels/Colombo/Room-2.png"]' WHERE hotel_id = 1 AND type_name = 'Deluxe Suite';
UPDATE room_types SET images = '["uploads/Hotels/Colombo/Room-3.png"]' WHERE hotel_id = 1 AND type_name = 'Presidential Suite';
UPDATE room_types SET images = '["uploads/Hotels/Colombo/Room-1.png"]' WHERE hotel_id = 1 AND type_name = 'Garden View Room';
UPDATE room_types SET images = '["uploads/Hotels/Colombo/Room-2.png"]' WHERE hotel_id = 1 AND type_name = 'Deluxe Queen Room';
UPDATE room_types SET images = '["uploads/Hotels/Colombo/Room-3.png"]' WHERE hotel_id = 1 AND type_name = 'Premier Room';
UPDATE room_types SET images = '["uploads/Hotels/Colombo/Room-2.png"]' WHERE hotel_id = 1 AND type_name = 'Mountain View Suite';

-- Ella rooms
UPDATE room_types SET images = '["uploads/Hotels/Ella/Room-1.png"]' WHERE hotel_id = 2 AND type_name = 'Garden View Room';
UPDATE room_types SET images = '["uploads/Hotels/Ella/Room-2.png"]' WHERE hotel_id = 2 AND type_name = 'Heritage Suite';
UPDATE room_types SET images = '["uploads/Hotels/Ella/Room-1.png"]' WHERE hotel_id = 2 AND type_name = 'Mountain View Suite';
UPDATE room_types SET images = '["uploads/Hotels/Ella/Room-1.png"]' WHERE hotel_id = 2 AND type_name = 'Standard Room';
UPDATE room_types SET images = '["uploads/Hotels/Ella/Room-2.png"]' WHERE hotel_id = 2 AND type_name = 'Deluxe Room';

-- Matara rooms
UPDATE room_types SET images = '["uploads/Hotels/Matara/Room-1.png"]' WHERE hotel_id = 3 AND type_name = 'Beach View Room';
UPDATE room_types SET images = '["uploads/Hotels/Matara/Room-2.png"]' WHERE hotel_id = 3 AND type_name = 'Ocean Suite';
UPDATE room_types SET images = '["uploads/Hotels/Matara/Room-3.png"]' WHERE hotel_id = 3 AND type_name = 'Presidential Suite';
UPDATE room_types SET images = '["uploads/Hotels/Matara/Room-1.png"]' WHERE hotel_id = 3 AND type_name = 'Standard Room';
UPDATE room_types SET images = '["uploads/Hotels/Matara/Room-2.png"]' WHERE hotel_id = 3 AND type_name = 'Deluxe Room';

-- Nuwara Eliya rooms
UPDATE room_types SET images = '["uploads/Hotels/Nuwara Eliya/Room-1.png"]' WHERE hotel_id = 4 AND type_name = 'Tea Garden Room';
UPDATE room_types SET images = '["uploads/Hotels/Nuwara Eliya/Room-2.png"]' WHERE hotel_id = 4 AND type_name = 'Hill Station Suite';
UPDATE room_types SET images = '["uploads/Hotels/Nuwara Eliya/Room-1.png"]' WHERE hotel_id = 4 AND type_name = 'Presidential Suite';
UPDATE room_types SET images = '["uploads/Hotels/Nuwara Eliya/Room-1.png"]' WHERE hotel_id = 4 AND type_name = 'Standard Room';
UPDATE room_types SET images = '["uploads/Hotels/Nuwara Eliya/Room-2.png"]' WHERE hotel_id = 4 AND type_name = 'Deluxe Room';

-- Update any remaining room types with default images
UPDATE room_types SET images = '["uploads/Hotels/Colombo/Room-1.png"]' WHERE images LIKE '%unsplash%' OR images = '' OR images IS NULL;

-- Verify updates
SELECT 'Hotels Updated:' as Status;
SELECT id, name, city, images FROM hotels ORDER BY city;

SELECT 'Room Types Updated:' as Status;
SELECT id, hotel_id, type_name, images FROM room_types ORDER BY hotel_id, type_name;

-- Note: Homepage image slider has been updated to use local images:
-- Slide 1: uploads/Hotels/Colombo/Hotel-1.png
-- Slide 2: uploads/Hotels/Ella/Hotel-1.png  
-- Slide 3: uploads/Hotels/Nuwara Eliya/Hotel-1.png
-- Slide 4: uploads/Hotels/Matara/Matara-1.png
-- Slide 5: uploads/Hotels/Colombo/Hotel-2.png
-- Slide 6: uploads/Hotels/Ella/Hotel-2.png

-- Note: About Flower Garden Hotels section now includes hotel images:
-- Colombo: uploads/Hotels/Colombo/Hotel-1.png
-- Ella: uploads/Hotels/Ella/Hotel-1.png
-- Matara: uploads/Hotels/Matara/Matara-1.png
-- Nuwara Eliya: uploads/Hotels/Nuwara Eliya/Hotel-1.png
