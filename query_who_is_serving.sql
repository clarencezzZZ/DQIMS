-- Run this SQL query to find who is serving
SELECT 
    i.id,
    i.queue_number,
    i.guest_name,
    i.status,
    i.priority,
    c.section,
    c.name as category_name
FROM inquiries i
LEFT JOIN categories c ON i.category_id = c.id
WHERE DATE(i.date) = CURDATE()
AND i.status IN ('serving', 'waiting')
ORDER BY 
    CASE WHEN i.status = 'serving' THEN 0 ELSE 1 END,
    i.created_at;
