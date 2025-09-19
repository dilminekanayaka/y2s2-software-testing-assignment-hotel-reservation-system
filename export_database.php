<?php
require_once 'config.php';

echo "<h1>🗄️ Flower Garden Hotels - Database Export Tool</h1>";

try {
    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>📊 Current Database: " . $dbname . "</h2>";
    echo "<p><strong>Tables found:</strong> " . count($tables) . "</p>";
    
    // Display tables
    echo "<h3>📋 Database Tables:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "<li><strong>$table</strong> - $count records</li>";
    }
    echo "</ul>";
    
    // Export options
    echo "<h2>📤 Export Options</h2>";
    
    echo "<div style='background: #f0f8ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>Option 1: Manual Export via phpMyAdmin</h3>";
    echo "<ol>";
    echo "<li>Go to <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
    echo "<li>Select database: <strong>$dbname</strong></li>";
    echo "<li>Click 'Export' tab</li>";
    echo "<li>Choose 'Custom' method</li>";
    echo "<li>Select all tables</li>";
    echo "<li>Choose 'SQL' format</li>";
    echo "<li>Check 'Add DROP TABLE statement'</li>";
    echo "<li>Check 'Add CREATE TABLE statement'</li>";
    echo "<li>Check 'Add INSERT statement'</li>";
    echo "<li>Click 'Go' to download</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='background: #f0fff0; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>Option 2: Automated Export (Download SQL File)</h3>";
    echo "<p>Click the button below to generate and download SQL file:</p>";
    echo "<form method='post' action=''>";
    echo "<input type='hidden' name='action' value='export'>";
    echo "<button type='submit' style='background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>📥 Download SQL Export</button>";
    echo "</form>";
    echo "</div>";
    
    // Handle export request
    if (isset($_POST['action']) && $_POST['action'] === 'export') {
        exportDatabase();
    }
    
    // Database connection details
    echo "<h2>🔗 Database Connection Details</h2>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>";
    echo "<p><strong>Host:</strong> $host</p>";
    echo "<p><strong>Database:</strong> $dbname</p>";
    echo "<p><strong>Username:</strong> $username</p>";
    echo "<p><strong>Password:</strong> " . (empty($password) ? '(empty)' : '***hidden***') . "</p>";
    echo "</div>";
    
    // Cloud deployment recommendations
    echo "<h2>☁️ Cloud Database Recommendations</h2>";
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;'>";
    
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 8px;'>";
    echo "<h3>🌍 PlanetScale</h3>";
    echo "<p><strong>Type:</strong> MySQL-compatible</p>";
    echo "<p><strong>Free Tier:</strong> Yes</p>";
    echo "<p><strong>Best for:</strong> Vercel deployment</p>";
    echo "<p><a href='https://planetscale.com' target='_blank'>Visit PlanetScale →</a></p>";
    echo "</div>";
    
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px;'>";
    echo "<h3>🚂 Railway</h3>";
    echo "<p><strong>Type:</strong> MySQL</p>";
    echo "<p><strong>Free Tier:</strong> Yes</p>";
    echo "<p><strong>Best for:</strong> Full-stack deployment</p>";
    echo "<p><a href='https://railway.app' target='_blank'>Visit Railway →</a></p>";
    echo "</div>";
    
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 8px;'>";
    echo "<h3>⚡ Supabase</h3>";
    echo "<p><strong>Type:</strong> PostgreSQL</p>";
    echo "<p><strong>Free Tier:</strong> Yes</p>";
    echo "<p><strong>Best for:</strong> Modern apps</p>";
    echo "<p><a href='https://supabase.com' target='_blank'>Visit Supabase →</a></p>";
    echo "</div>";
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #ffebee; padding: 20px; border-radius: 8px; border-left: 4px solid #f44336;'>";
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection in config.php</p>";
    echo "</div>";
}

function exportDatabase() {
    global $pdo, $dbname;
    
    try {
        // Set headers for file download
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="flower-garden-hotels-database.sql"');
        
        echo "-- Flower Garden Hotels Database Export\n";
        echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
        echo "-- Database: $dbname\n\n";
        
        // Get all tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            echo "-- Table structure for table `$table`\n";
            echo "DROP TABLE IF EXISTS `$table`;\n";
            
            // Get table structure
            $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
            echo $createTable['Create Table'] . ";\n\n";
            
            // Get table data
            echo "-- Data for table `$table`\n";
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                echo "INSERT INTO `$table` ($columnList) VALUES\n";
                
                $values = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $rowValues[] = 'NULL';
                        } else {
                            $rowValues[] = $pdo->quote($value);
                        }
                    }
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }
                
                echo implode(",\n", $values) . ";\n\n";
            } else {
                echo "-- No data in table `$table`\n\n";
            }
        }
        
        echo "-- Export completed successfully!\n";
        exit;
        
    } catch (Exception $e) {
        echo "<div style='background: #ffebee; padding: 20px; border-radius: 8px; border-left: 4px solid #f44336;'>";
        echo "<p style='color: red;'><strong>❌ Export Error:</strong> " . $e->getMessage() . "</p>";
        echo "</div>";
    }
}
?>
