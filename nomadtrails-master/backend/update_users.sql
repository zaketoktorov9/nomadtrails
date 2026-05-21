USE kgviptravel;

ALTER TABLE users ADD COLUMN phone VARCHAR(50) AFTER email;
ALTER TABLE users ADD COLUMN preferences TEXT AFTER role;
