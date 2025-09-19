<?php
/**
 * Database Connection Test Tool
 * Tests connection to various database providers
 */

echo "<h1>🔗 Database Connection Test Tool</h1>";

// Test configurations for different providers
$testConfigs = [
    'Local XAMPP' => [
        'host' => 'localhost',
        'dbname' => 'ellaflowergarden',
        'username' => 'root',
        'password' => '',
        'port' => 3306
    ],
    'PlanetScale' => [
        'host' => 'your-planetscale-host',
        'dbname' => 'your-database-name',
        'username' => 'your-username',
        'password' => 'your-password',
        'port' => 3306
    ],
    'Railway MySQL' => [
        'host' => 'your-railway-host',
        'dbname' => 'railway',
        'username' => 'root',
        'password' => 'your-railway-password',
        'port' => 3306
    ],
    'Supabase (PostgreSQL)' => [
        'host' => 'your-supabase-host',
        'dbname' => 'postgres',
        'username' => 'postgres',
        'password' => 'your-supabase-password',
        'port' => 5432
    ]
];

echo "<h2>🧪 Test Database Connections</h2>";

foreach ($testConfigs as $provider => $config) {
    echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<h3>🔍 Testing: $provider</h3>";
    
    try {
        if ($provider === 'Supabase (PostgreSQL)') {
            // PostgreSQL connection
            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
            $pdo = new PDO($dsn, $config['username'], $config['password']);
        } else {
            // MySQL connection
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['username'], $config['password']);
        }
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color: green;'>✅ <strong>Connection Successful!</strong></p>";
        
        // Test query
        if ($provider === 'Supabase (PostgreSQL)') {
            $result = $pdo->query("SELECT version()")->fetchColumn();
            echo "<p><strong>Database Version:</strong> $result</p>";
        } else {
            $result = $pdo->query("SELECT VERSION()")->fetchColumn();
            echo "<p><strong>MySQL Version:</strong> $result</p>";
            
            // Check if our tables exist
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "<p><strong>Tables Found:</strong> " . count($tables) . "</p>";
            
            if (count($tables) > 0) {
                echo "<p><strong>Table List:</strong> " . implode(', ', $tables) . "</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ <strong>Connection Failed:</strong> " . $e->getMessage() . "</p>";
        
        if ($provider === 'Local XAMPP') {
            echo "<p><strong>💡 Troubleshooting:</strong></p>";
            echo "<ul>";
            echo "<li>Make sure XAMPP is running</li>";
            echo "<li>Check if MySQL service is started</li>";
            echo "<li>Verify database 'ellaflowergarden' exists</li>";
            echo "<li>Check username/password in config.php</li>";
            echo "</ul>";
        }
    }
    
    echo "</div>";
}

echo "<h2>📋 Connection String Examples</h2>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🔧 For config.php</h3>";
echo "<pre style='background: #e9ecef; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
echo "<?php\n";
echo "// Local XAMPP\n";
echo "\$host = 'localhost';\n";
echo "\$dbname = 'ellaflowergarden';\n";
echo "\$username = 'root';\n";
echo "\$password = '';\n\n";

echo "// PlanetScale\n";
echo "\$host = 'your-planetscale-host';\n";
echo "\$dbname = 'your-database-name';\n";
echo "\$username = 'your-username';\n";
echo "\$password = 'your-password';\n\n";

echo "// Railway MySQL\n";
echo "\$host = 'your-railway-host';\n";
echo "\$dbname = 'railway';\n";
echo "\$username = 'root';\n";
echo "\$password = 'your-railway-password';\n";
echo "?>";
echo "</pre>";
echo "</div>";

echo "<h2>🌐 Environment Variables for Deployment</h2>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>Vercel Environment Variables</h3>";
echo "<pre style='background: #e9ecef; padding: 15px; border-radius: 5px;'>";
echo "DB_HOST=your-cloud-database-host\n";
echo "DB_NAME=your-database-name\n";
echo "DB_USER=your-username\n";
echo "DB_PASS=your-password\n";
echo "WEBSITE_URL=https://your-app.vercel.app";
echo "</pre>";
echo "</div>";

echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>Railway Environment Variables</h3>";
echo "<pre style='background: #e9ecef; padding: 15px; border-radius: 5px;'>";
echo "DATABASE_URL=mysql://username:password@host:port/database\n";
echo "DB_HOST=your-railway-host\n";
echo "DB_NAME=railway\n";
echo "DB_USER=root\n";
echo "DB_PASS=your-railway-password";
echo "</pre>";
echo "</div>";

echo "<h2>📚 Next Steps</h2>";
echo "<ol>";
echo "<li><strong>Export your database</strong> using <a href='export_database.php'>export_database.php</a></li>";
echo "<li><strong>Choose a cloud database provider</strong> (PlanetScale, Railway, Supabase)</li>";
echo "<li><strong>Import your database</strong> to the cloud provider</li>";
echo "<li><strong>Update config.php</strong> with cloud database details</li>";
echo "<li><strong>Deploy your application</strong> to Vercel/Railway</li>";
echo "<li><strong>Configure environment variables</strong> in deployment platform</li>";
echo "</ol>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;'>";
echo "<h3>⚠️ Important Notes</h3>";
echo "<ul>";
echo "<li><strong>Never commit database credentials</strong> to GitHub</li>";
echo "<li><strong>Use environment variables</strong> for production</li>";
echo "<li><strong>Test connections locally</strong> before deploying</li>";
echo "<li><strong>Backup your database</strong> before migration</li>";
echo "</ul>";
echo "</div>";
?>
