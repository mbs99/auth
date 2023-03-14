insert into scopes (id, name, description) VALUES (null, 'basic', null);
insert into clients (id, identifier, name, redirect_uri, is_confidential) VALUES (null, 'test', 'test', 'http://foo.bar', 1);
insert into users (id, username, password, client_id) VALUES (null, 'test', '$2y$10$FM2wrJQ./wd9giXwkjx1nOW7eP8Mg1v5zdQYvrWN2bUEf0zRRzu0q', 1);