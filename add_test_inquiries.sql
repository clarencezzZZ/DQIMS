-- Add test inquiry data for March 2026
-- Run this with: php artisan db:table inquiries

INSERT INTO inquiries (category_id, name, address, request_type, priority, status, date, queue_number, served_by, served_at, completed_at, created_at, updated_at)
SELECT 
    (SELECT id FROM categories WHERE is_active = true LIMIT 1) as category_id,
    'Juan Dela Cruz' as name,
    'Manila' as address,
    'walk-in' as request_type,
    'normal' as priority,
    'completed' as status,
    '2026-03-05' as date,
    CONCAT('Q-', FLOOR(RAND() * 9000 + 1000)) as queue_number,
    2 as served_by,
    '2026-03-05 10:00:00' as served_at,
    '2026-03-05 11:00:00' as completed_at,
    NOW() as created_at,
    NOW() as updated_at
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM inquiries WHERE name = 'Juan Dela Cruz' AND date = '2026-03-05'
);

INSERT INTO inquiries (category_id, name, address, request_type, priority, status, date, queue_number, served_by, served_at, completed_at, created_at, updated_at)
SELECT 
    (SELECT id FROM categories WHERE is_active = true LIMIT 1) as category_id,
    'Maria Santos' as name,
    'Quezon City' as address,
    'online' as request_type,
    'high' as priority,
    'completed' as status,
    '2026-03-10' as date,
    CONCAT('Q-', FLOOR(RAND() * 9000 + 1000)) as queue_number,
    2 as served_by,
    '2026-03-10 14:00:00' as served_at,
    '2026-03-10 15:00:00' as completed_at,
    NOW() as created_at,
    NOW() as updated_at
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM inquiries WHERE name = 'Maria Santos' AND date = '2026-03-10'
);

INSERT INTO inquiries (category_id, name, address, request_type, priority, status, date, queue_number, served_by, served_at, completed_at, created_at, updated_at)
SELECT 
    (SELECT id FROM categories WHERE is_active = true LIMIT 1) as category_id,
    'Pedro Reyes' as name,
    'Makati' as address,
    'walk-in' as request_type,
    'normal' as priority,
    'waiting' as status,
    '2026-03-15' as date,
    CONCAT('Q-', FLOOR(RAND() * 9000 + 1000)) as queue_number,
    NULL as served_by,
    NULL as served_at,
    NULL as completed_at,
    NOW() as created_at,
    NOW() as updated_at
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM inquiries WHERE name = 'Pedro Reyes' AND date = '2026-03-15'
);

INSERT INTO inquiries (category_id, name, address, request_type, priority, status, date, queue_number, served_by, served_at, completed_at, created_at, updated_at)
SELECT 
    (SELECT id FROM categories WHERE is_active = true LIMIT 1) as category_id,
    'Ana Garcia' as name,
    'Pasig' as address,
    'online' as request_type,
    'normal' as priority,
    'completed' as status,
    '2026-03-20' as date,
    CONCAT('Q-', FLOOR(RAND() * 9000 + 1000)) as queue_number,
    2 as served_by,
    '2026-03-20 09:00:00' as served_at,
    '2026-03-20 10:00:00' as completed_at,
    NOW() as created_at,
    NOW() as updated_at
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM inquiries WHERE name = 'Ana Garcia' AND date = '2026-03-20'
);

INSERT INTO inquiries (category_id, name, address, request_type, priority, status, date, queue_number, served_by, served_at, completed_at, created_at, updated_at)
SELECT 
    (SELECT id FROM categories WHERE is_active = true LIMIT 1) as category_id,
    'Jose Ramos' as name,
    'Taguig' as address,
    'walk-in' as request_type,
    'high' as priority,
    'skipped' as status,
    '2026-03-25' as date,
    CONCAT('Q-', FLOOR(RAND() * 9000 + 1000)) as queue_number,
    NULL as served_by,
    NULL as served_at,
    NULL as completed_at,
    NOW() as created_at,
    NOW() as updated_at
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM inquiries WHERE name = 'Jose Ramos' AND date = '2026-03-25'
);
