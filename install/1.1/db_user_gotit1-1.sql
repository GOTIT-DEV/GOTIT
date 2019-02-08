-- db_user_gotit1-1 : Default admin account TO CHANGE : login = admin / password = adminGOTIT
INSERT INTO public.user_db(
	 id, username, password, email, role, salt, name, institution, date_cre, date_maj, user_cre, user_maj, is_active, commentaire_user)
	VALUES (1, 'admin', 'O3nuhNYmU/1ZZEH3pt2kThF1HbPzjUVSpX0UFTBxaN7y+1Qmqsbs+KzL4Hu2xXTVXsUZ87XkdXOD4Jykw4CEIQ==', 'admin@institution.fr','ROLE_ADMIN', null, 'admin name', 'institution', null, null, 1, 1 , 1, 'Default admin account TO CHANGE : login = admin / password = adminGOTIT')
	;