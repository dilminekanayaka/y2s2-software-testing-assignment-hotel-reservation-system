<?php
require_once 'config.php';

echo "<h1>🌸 Updating Flower Garden Hotels Database Images</h1>";

try {
    // Update hotel images to use local paths
    $hotel_updates = [
        1 => [ // Colombo
            'images' => '["uploads/Hotels/Colombo/Hotel-1.png", "uploads/Hotels/Colombo/Hotel-2.png", "uploads/Hotels/Colombo/Hotel-3.png"]'
        ],
        2 => [ // Ella
            'images' => '["uploads/Hotels/Ella/Hotel-1.png", "uploads/Hotels/Ella/Hotel-2.png"]'
        ],
        3 => [ // Matara
            'images' => '["uploads/Hotels/Matara/Matara-1.png", "uploads/Hotels/Matara/Matara-2.png"]'
        ],
        4 => [ // Nuwara Eliya
            'images' => '["uploads/Hotels/Nuwara Eliya/Hotel-1.png", "uploads/Hotels/Nuwara Eliya/Hotel-2.png", "uploads/Hotels/Nuwara Eliya/Hotel-3.png", "uploads/Hotels/Nuwara Eliya/Hotel-4.png"]'
        ]
    ];
    
    echo "<h2>🏨 Updating Hotel Images</h2>";
    foreach ($hotel_updates as $hotel_id => $data) {
        $stmt = $pdo->prepare("UPDATE hotels SET images = ? WHERE id = ?");
        $stmt->execute([$data['images'], $hotel_id]);
        echo "<p>✅ Updated hotel $hotel_id images</p>";
    }
    
    // Update room type images to use local paths
    $room_updates = [
        // Colombo rooms
        1 => '["uploads/Hotels/Colombo/Room-1.png"]', // Standard Room
        2 => '["uploads/Hotels/Colombo/Room-2.png"]', // Deluxe Suite
        3 => '["uploads/Hotels/Colombo/Room-3.png"]', // Presidential Suite
        
        // Ella rooms
        4 => '["uploads/Hotels/Ella/Room-1.png"]', // Garden View Room
        5 => '["uploads/Hotels/Ella/Room-2.png"]', // Heritage Suite
        
        // Matara rooms
        6 => '["uploads/Hotels/Matara/Room-1.png"]', // Beach View Room
        7 => '["uploads/Hotels/Matara/Room-2.png"]', // Ocean Suite
        
        // Nuwara Eliya rooms
        8 => '["uploads/Hotels/Nuwara Eliya/Room-1.png"]', // Tea Garden Room
        9 => '["uploads/Hotels/Nuwara Eliya/Room-2.png"]'  // Mountain Suite
    ];
    
    echo "<h2>🛏️ Updating Room Type Images</h2>";
    foreach ($room_updates as $room_id => $images) {
        $stmt = $pdo->prepare("UPDATE room_types SET images = ? WHERE id = ?");
        $stmt->execute([$images, $room_id]);
        echo "<p>✅ Updated room type $room_id images</p>";
    }
    
    echo "<h2>🔍 Verification</h2>";
    
    // Verify hotel images
    $stmt = $pdo->query("SELECT id, name, images FROM hotels ORDER BY id");
    $hotels = $stmt->fetchAll();
    
    echo "<h3>🏨 Hotel Images:</h3>";
    foreach ($hotels as $hotel) {
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; background: #f9f9f9;'>";
        echo "<h4>🌸 {$hotel['name']}</h4>";
        echo "<p><strong>Images:</strong> {$hotel['images']}</p>";
        
        $images = json_decode($hotel['images'], true);
        if ($images) {
            echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
            foreach ($images as $image) {
                echo "<img src='{$image}' style='width: 120px; height: 90px; border: 2px solid #ddd; border-radius: 6px; object-fit: cover;' onerror=\"this.style.border='3px solid red'; this.alt='BROKEN: {$image}'\">";
            }
            echo "</div>";
        }
        echo "</div>";
    }
    
    // Verify room images
    $stmt = $pdo->query("SELECT id, type_name, images FROM room_types ORDER BY id");
    $rooms = $stmt->fetchAll();
    
    echo "<h3>🛏️ Room Type Images:</h3>";
    foreach ($rooms as $room) {
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; background: #f9f9f9;'>";
        echo "<h4>🏠 {$room['type_name']}</h4>";
        echo "<p><strong>Images:</strong> {$room['images']}</p>";
        
        $images = json_decode($room['images'], true);
        if ($images) {
            echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
            foreach ($images as $image) {
                echo "<img src='{$image}' style='width: 120px; height: 90px; border: 2px solid #ddd; border-radius: 6px; object-fit: cover;' onerror=\"this.style.border='3px solid red'; this.alt='BROKEN: {$image}'\">";
            }
            echo "</div>";
        }
        echo "</div>";
    }
    
    echo "<h2>🎉 Database Image Update Completed!</h2>";
    echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; border-left: 4px solid #4caf50;'>";
    echo "<p><strong>✅ Success!</strong> All hotel and room images have been updated to use local paths.</p>";
    echo "<p><strong>📁 Image Directory:</strong> uploads/Hotels/</p>";
    echo "<p><strong>🖼️ Total Images:</strong> " . (count($hotel_updates) + count($room_updates)) . " image sets updated</p>";
    echo "<p><strong>🗑️ Next Step:</strong> You can now delete this file: update_database_images.php</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #ffebee; padding: 20px; border-radius: 8px; border-left: 4px solid #f44336;'>";
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
