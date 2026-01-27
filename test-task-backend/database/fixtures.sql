-- Projects
INSERT INTO project (title) VALUES ('Website Redesign');
INSERT INTO project (title) VALUES ('Mobile App Launch');
INSERT INTO project (title) VALUES ('Marketing Campaign');

-- Tasks for Website Redesign (Project 1)
INSERT INTO task (project_id, title, status) VALUES (1, 'Design Home Page', 'done');
INSERT INTO task (project_id, title, status) VALUES (1, 'Develop API', 'in_progress');
INSERT INTO task (project_id, title, status) VALUES (1, 'Frontend Integration', 'todo');

-- Tasks for Mobile App Launch (Project 2)
INSERT INTO task (project_id, title, status) VALUES (2, 'Setup React Native', 'done');
INSERT INTO task (project_id, title, status) VALUES (2, 'App Store Submission', 'todo');

-- Tasks for Marketing Campaign (Project 3)
INSERT INTO task (project_id, title, status) VALUES (3, 'Create Ad Banners', 'todo');
