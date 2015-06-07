CREATE DATABASE contacts_api;

USE contacts_api;

CREATE TABLE contacts
(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    mobile VARCHAR(14) NOT NULL
);
	