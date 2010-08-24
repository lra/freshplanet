CREATE TABLE hiscore (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER UNSIGNED, time INTEGER NOT NULL, boardwidth INTEGER NOT NULL, created_at DATETIME NOT NULL);
CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT, email VARCHAR(255) UNIQUE, firstname VARCHAR(255), lastname VARCHAR(255), game_board LONGBLOB, game_start DATETIME, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL);
CREATE INDEX userindex_idx ON hiscore (user_id);
CREATE INDEX createdindex_idx ON hiscore (created_at);
CREATE INDEX boardwidthtimeindex_idx ON hiscore (boardwidth, time);
CREATE INDEX createdindex_idx ON user (created_at);
