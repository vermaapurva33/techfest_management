CREATE DATABASE techfest_db;
USE techfest_db;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Stores Hashed Password
    role ENUM('participant', 'organizer', 'judge') DEFAULT 'participant',
    phone VARCHAR(15)
);

CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATE,
    image_path VARCHAR(255),
    organizer_id INT,
    FOREIGN KEY (organizer_id) REFERENCES users(user_id)
);

CREATE TABLE registrations (
    reg_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_id INT,
    reg_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id),
    UNIQUE(user_id, event_id) -- Prevents double registration
);