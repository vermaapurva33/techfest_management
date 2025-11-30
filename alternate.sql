-- 1. INDEPENDENT TABLES (Create these first) --

CREATE TABLE participants (
    participant_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    college VARCHAR(150) NOT NULL,
    department VARCHAR(100) NOT NULL,
    year VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE mentors (
    mentor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    designation VARCHAR(100) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clubs (
    club_id INT AUTO_INCREMENT PRIMARY KEY,
    club_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABLES WITH DEPENDENCIES (Events depend on Clubs) --

CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    description TEXT,
    entry_fee DECIMAL(10,2) DEFAULT 0.00,
    club_id INT, -- Linked to Clubs
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES clubs(club_id) ON DELETE SET NULL
);

-- 3. COMPLEX DEPENDENCIES (Teams depend on Participants & Mentors) --

CREATE TABLE teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    tname VARCHAR(100) NOT NULL,
    leader INT, 
    mentor INT, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader) REFERENCES participants(participant_id) ON DELETE SET NULL,
    FOREIGN KEY (mentor) REFERENCES mentors(mentor_id) ON DELETE SET NULL
);

-- 4. LINKING TABLES (Forms connect Participants to Teams) --

CREATE TABLE forms (
    p_id INT,
    t_id INT,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (p_id, t_id),
    FOREIGN KEY (p_id) REFERENCES participants(participant_id) ON DELETE CASCADE,
    FOREIGN KEY (t_id) REFERENCES teams(team_id) ON DELETE CASCADE
);

-- 5. EVENT ROLES (Depend on Events) --

CREATE TABLE coordinators (
    coordinator_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(150),
    event_id INT,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE SET NULL
);

CREATE TABLE judges (
    judge_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    affiliation VARCHAR(150) NOT NULL, 
    expertise VARCHAR(100), 
    email VARCHAR(150),
    event_id INT,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE SET NULL
);

CREATE TABLE volunteers (
    volunteer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    role_description VARCHAR(100),
    event_id INT,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE SET NULL
);