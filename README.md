# Profiles

Sample CRUD application that allows users to add resume profile entries. All users can view all profiles, but they can only be edited by the user who created them.


It assumes the following queries have been run on a local MySQL database and that at least 1 user exists:

CREATE DATABASE profiledb;

CREATE TABLE users (
    user_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128),
    email VARCHAR(128),
    pass VARCHAR(128),
    INDEX(name),
    INDEX(pass)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE profiles (
    profile_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INTEGER UNSIGNED NOT NULL,
    first_name TEXT,
    last_name TEXT,
    email TEXT,
    headline TEXT,
    summary TEXT,
    PRIMARY KEY(profile_id),
    CONSTRAINT profile_ibfk_2
    	FOREIGN KEY (user_id)
    	REFERENCES users (user_id)
    	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE positions (
    position_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    profile_id INTEGER UNSIGNED NOT NULL,
    rank INTEGER,
    year INTEGER,
    description TEXT,
    PRIMARY KEY(position_id),
    CONSTRAINT position_ibfk_1
    	FOREIGN KEY(profile_id)
    	REFERENCES profiles(profile_id)
    	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = INNODB DEFAULT CHARSET=utf8;
