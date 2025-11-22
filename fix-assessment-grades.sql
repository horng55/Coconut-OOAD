-- Check if assessment 3 exists and what class it belongs to
SELECT * FROM assessments WHERE id = 3;

-- Check if there are any grades for assessment 3
SELECT * FROM grades WHERE assessment_id = 3;

-- Check students enrolled in the class that assessment 3 belongs to
-- Replace X with the class_id from the first query
-- SELECT s.id, u.first_name, u.last_name, u.email 
-- FROM students s
-- JOIN users u ON s.user_id = u.id
-- JOIN enrollments e ON s.id = e.student_id
-- WHERE e.class_id = X AND e.status = 'active';

-- If no grades exist, create them for all enrolled students
-- Replace X with the class_id and Y with the teacher's user_id
-- INSERT INTO grades (student_id, class_id, assessment_id, score, graded_by, created_at, updated_at)
-- SELECT s.id, e.class_id, 3, 0, Y, datetime('now'), datetime('now')
-- FROM students s
-- JOIN enrollments e ON s.id = e.student_id
-- WHERE e.class_id = X AND e.status = 'active';
