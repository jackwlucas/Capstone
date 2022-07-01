DROP TABLE IF EXISTS usersgroups;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS groups;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS announcements;

CREATE TABLE users (
  uid INT AUTO_INCREMENT,
  fName VARCHAR(128),
  lName VARCHAR(64),
  uName VARCHAR(64),
  email VARCHAR(64),
  dorm VARCHAR(64),
  gender VARCHAR(16),
  description VARCHAR(255),
  PRIMARY KEY (uid)
) ENGINE=InnoDB;

CREATE TABLE groups (
	gid INT AUTO_INCREMENT,
	title VARCHAR(50),
	description VARCHAR(255),
	PRIMARY KEY (gid)
) ENGINE=InnoDB;

CREATE TABLE usersgroups (
    uid INT NOT NULL,
    gid INT NOT NULL,
    FOREIGN KEY (uid) REFERENCES users(uid),
    FOREIGN KEY (gid) REFERENCES groups(gid)
) ENGINE=InnoDB;

CREATE TABLE events (
	eid INT AUTO_INCREMENT,
	title VARCHAR(64),
	location VARCHAR(64),
	date VARCHAR(16),
    time VARCHAR(16),
	description VARCHAR(255),
	gid INT,
	PRIMARY KEY (eid),
	FOREIGN KEY (gid) REFERENCES groups(gid)
) ENGINE=InnoDB;

CREATE TABLE announcements (
	aid INT NOT NULL AUTO_INCREMENT,
	title VARCHAR(50),
	description VARCHAR(255),
	gid INT,
	PRIMARY KEY (aid),
	FOREIGN KEY (gid) REFERENCES groups(gid)
) ENGINE=InnoDB;

CREATE TABLE maintenancetickets (
    mid INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(64) NOT NULL,
    dorm VARCHAR(64) NOT NULL,
    floor VARCHAR(64) NOT NULL,
    room INT NOT NULL,
    description VARCHAR(255),
    timestamp DATETIME,
    uid INT NOT NULL,
    PRIMARY KEY (mid),
    FOREIGN KEY (uid) REFERENCES users(uid)
) ENGINE=InnoDB;