create table clients (
    id int not null AUTO_INCREMENT PRIMARY KEY,
    identifier varchar(100) not null UNIQUE,
    name varchar(100) not null,
    redirect_url varchar(255) not null,
    is_confidential int
);
