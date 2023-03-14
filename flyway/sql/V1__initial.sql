create table clients (
    id int not null AUTO_INCREMENT PRIMARY KEY,
    identifier varchar(100) not null UNIQUE,
    name varchar(100) not null,
    redirect_uri varchar(255) not null,
    is_confidential int
);

create table scopes (
    id int not null AUTO_INCREMENT PRIMARY KEY,
    name varchar(100) not null UNIQUE,
    description varchar(255)
);

create table users (
    id int not null AUTO_INCREMENT PRIMARY KEY,
    username varchar(100) not null,
    password varchar(100) not null,
    client_id int not null,
    CONSTRAINT username_client UNIQUE (username, client_id)
);

ALTER TABLE users
ADD CONSTRAINT users_client_id_fk
FOREIGN KEY (client_id) REFERENCES clients (id);

create table auth_codes (
    id int not null AUTO_INCREMENT PRIMARY KEY,
    user_id int not null,
    client_id int not null,
    redirect_uri varchar(255) not null
);

ALTER TABLE auth_codes
ADD CONSTRAINT auth_codes_user_id_fk
FOREIGN KEY (user_id) REFERENCES users (id);
