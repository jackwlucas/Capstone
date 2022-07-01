# USER DATA
INSERT INTO users (fName, lName, uName, email, dorm, gender, description)
VALUES ('Joshia', 'Neissen', 'jneissen0', 'jneissen0@intel.com', 'Lunas', 'Male', 'Budget/Accounting Analyst I'),
       ('Claudie', 'Easbie', 'ceasbie1', 'ceasbie1@istockphoto.com', 'Xieji', 'Male', 'Environmental Tech'),
       ('Norma', 'Terry', 'nterry2', 'nterry2@netscape.com', 'London', 'Male', 'Research Assistant IV'),
       ('Lothario', 'Housen', 'lhousen3', 'lhousen3@upenn.edu', 'Mayapusi', 'Male', 'Registered Nurse'),
       ('Jeffy', 'Elmar', 'jelmar4', 'jelmar4@mysql.com', 'Besukrejo', 'Male', 'Technical Writer'),
       ('Shawnee', 'Dorgan', 'sdorgan5', 'sdorgan5@drupal.org', 'Samobor', 'Male', 'Human Resources Assistant IV'),
       ('Verile', 'Lunam', 'vlunam6', 'vlunam6@arstechnica.com', 'Pekuncen', 'Female', 'Recruiter'),
       ('Lorenzo', 'Sawford', 'lsawford7', 'lsawford7@baidu.com', 'Lachute', 'Male', 'Technical Writer'),
       ('Leann', 'Gasticke', 'lgasticke8', 'lgasticke8@google.pl', 'Buga', 'Male', 'VP Accounting'),
       ('Martelle', 'Zoellner', 'mzoellner9', 'mzoellner9@npr.org', 'Dowsk', 'Non-binary', 'Dental Hygienist');

# GROUP DATA
INSERT INTO groups (title, description)
VALUES ('Programming Pals', 'A group for aspiring programmers to learn and program together.'),
       ('Food Friends', 'Come join Food Friends! We discuss our favorite foods and recipes.'),
       ('Gamers Unite', 'Attention all gamers! Prove your worth in our fierce gauntlet!'),
       ('Dog Lovers', 'If you love dogs, this group is for you!'),
       ('Catan', 'Come discuss the complex game of Catan and share new strategies!');

# USERSGROUPS DATA
INSERT INTO usersgroups (uid, gid)
VALUES (20, 1),
       (22, 1),
       (25, 2),
       (26, 2),
       (20, 3),
       (22, 3),
       (25, 4),
       (26, 4),
       (20, 5),
       (22, 5),
       (25, 7),
       (26, 7);