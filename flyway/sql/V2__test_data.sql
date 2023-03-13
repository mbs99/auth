insert into scopes (id, name, description) VALUES (null, 'basic', null);
insert into clients (id, identifier, name, redirect_uri, is_confidential) VALUES (null, 'test', 'test', 'http://foo.bar', 1);
insert into users (id, name, password, client_id) VALUES (null, 'test', '$2a$12$VTmd68H4Xq2PwM5u.graz.JOHFLW/Qs23pU7z4lpaYl4QVnLFJkoK', 1);