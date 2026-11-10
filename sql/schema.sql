DROP TABLE IF EXISTS user_competitions;
DROP TABLE IF EXISTS access_tokens;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS event_broadcaster;
DROP TABLE IF EXISTS competition_broadcaster;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS broadcasters;
DROP TABLE IF EXISTS prova;
DROP TABLE IF EXISTS competitions;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS sport;

-- Sport

CREATE TABLE sport(

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    image_path VARCHAR(255) DEFAULT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Category

CREATE TABLE categories(

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    sport_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,

    FOREIGN KEY (sport_id) REFERENCES sport(id) ON DELETE CASCADE,

    UNIQUE (sport_id, name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Competitions

CREATE TABLE competitions(

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,

    FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE CASCADE,

    UNIQUE (category_id, name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Broadcasters

CREATE TABLE broadcasters(

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    type ENUM ('free_tv', 'pay_tv', 'streaming') NOT NULL,
    website_url VARCHAR(255) NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events

CREATE TABLE events(

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    competition_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    location varchar(150) NULL,
    start_utc DATETIME NOT NULL,
    end_utc DATETIME NULL,
    status ENUM ('confirmed', 'to_verify', 'postponed', 'cancelled') NOT NULL DEFAULT 'confirmed',
    natural_key VARCHAR(191) NOT NULL, 
    source_url VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY(competition_id) REFERENCES competitions(id) ON DELETE CASCADE,

    UNIQUE(natural_key),
    INDEX (competition_id, start_utc)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Competition_broadcaster

CREATE TABLE competition_broadcaster(

    competition_id INT UNSIGNED NOT NULL,
    broadcaster_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (competition_id, broadcaster_id),
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    FOREIGN KEY (broadcaster_id) REFERENCES broadcasters(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Event_broadcaster

CREATE TABLE event_broadcaster(

    event_id INT UNSIGNED NOT NULL,
    broadcaster_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (event_id, broadcaster_id),
    FOREIGN KEY(event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY(broadcaster_id) REFERENCES broadcasters(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Users

CREATE TABLE users(

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    timezone VARCHAR(64) NOT NULL DEFAULT 'Europe/Rome',
    feed_token CHAR(64),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(email),
    UNIQUE(feed_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User_competitions

CREATE TABLE user_competitions(

    user_id INT UNSIGNED NOT NULL,
    competition_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (user_id, competition_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Access_tokens 

CREATE TABLE access_tokens(

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    UNIQUE(token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;