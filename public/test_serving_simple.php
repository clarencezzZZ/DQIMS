<?php
// Simple direct database check - run via browser: http://localhost/DQIMS/test_serving_simple.php

$host = 'localhost';
$dbname = 'dqims'; // Adjust if different
$username = 'root'; // XAMPP default
$password = ''; // XAMPP default no password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>WHO IS SERVING? - Direct DB Check</h1>";
    
    // Query 1: Who is serving today?
    $stmt = $pdo->query("
        SELECT i.id, i.queue_number, i.guest_name, i.status, i.priority, 
               c.section, c.name as category_name
        FROM inquiries i
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE DATE(i.date) = CURDATE()
        AND i.status = 'serving'
    ");
    
    $serving = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Currently Serving:</h2>";
    if (empty($serving)) {
        echo "<p style='color: red; font-weight: bold;'>❌ NO ONE IS CURRENTLY SERVING</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Queue #</th><th>Name</th><th>Status</th><th>Priority</th><th>Section</th></tr>";
        foreach ($serving as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['queue_number']}</td>";
            echo "<td>{$row['guest_name']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "<td>{$row['priority']}</td>";
            echo "<td>{$row['section']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Query 2: Waiting in Aggregate and Correction Section
    echo "<h2>Waiting in Aggregate and Correction Section:</h2>";
    $stmt = $pdo->query("
        SELECT i.id, i.queue_number, i.guest_name, i.priority, i.created_at, c.section
        FROM inquiries i
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE DATE(i.date) = CURDATE()
        AND c.section = 'Aggregate and Correction Section'
        AND i.status = 'waiting'
        ORDER BY i.created_at
    ");
    
    $waiting = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($waiting)) {
        echo "<p>No waiting inquiries in this section</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Queue #</th><th>Name</th><th>Priority</th><th>Created</th></tr>";
        foreach ($waiting as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['queue_number']}</td>";
            echo "<td>{$row['guest_name']}</td>";
            echo "<td>{$row['priority']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check Joshua specifically
        echo "<h2>Joshua's Inquiry (ID: 65):</h2>";
        $stmt = $pdo->query("SELECT id, queue_number, guest_name, status, priority, category_id FROM inquiries WHERE id = 65");
        $joshua = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($joshua) {
            echo "<ul>";
            echo "<li>ID: {$joshua['id']}</li>";
            echo "<li>Queue #: {$joshua['queue_number']}</li>";
            echo "<li>Name: {$joshua['guest_name']}</li>";
            echo "<li>Status: {$joshua['status']}</li>";
            echo "<li>Priority: {$joshua['priority']}</li>";
            echo "<li>Category ID: {$joshua['category_id']}</li>";
            echo "</ul>";
            
            // Get category section
            $stmt = $pdo->query("SELECT section FROM categories WHERE id = {$joshua['category_id']}");
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>Category Section: <strong>{$category['section']}</strong></p>";
            
            // Is Joshua first?
            $isFirst = ($waiting[0]['id'] == 65);
            echo "<p style='font-size: 1.2em;'>Is Joshua first in queue? " . ($isFirst ? "<span style='color: green; font-weight: bold;'>✅ YES</span>" : "<span style='color: red; font-weight: bold;'>❌ NO</span>") . "</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Check your database credentials in this file</p>";
}
?>
