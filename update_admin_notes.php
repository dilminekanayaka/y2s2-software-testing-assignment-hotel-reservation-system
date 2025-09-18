<?php
require_once 'config.php';

try {
    // Add admin_notes column to reservations table
    $sql = "ALTER TABLE reservations ADD COLUMN admin_notes TEXT NULL AFTER special_requests";
    $pdo->exec($sql);
    echo "✅ Successfully added admin_notes column to reservations table\n";
    
    // Add index for better performance
    $sql = "CREATE INDEX idx_reservations_admin_notes ON reservations(admin_notes(100))";
    $pdo->exec($sql);
    echo "✅ Successfully added index for admin_notes column\n";
    
    echo "\n🎉 Database update completed successfully!\n";
    echo "You can now delete this file: update_admin_notes.php\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✅ admin_notes column already exists\n";
    } elseif (strpos($e->getMessage(), 'Duplicate key name') !== false) {
        echo "✅ Index already exists\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}
?>
