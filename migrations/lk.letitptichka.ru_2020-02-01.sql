INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`)
VALUES
	('manager','4',1571235640),
	('manager','5',1580035129),
	('manager','8',1571918926);

INSERT INTO `exception` (`id`, `name`, `status`, `created_at`, `updated_at`)
VALUES
	(5,'Сахар',10,1575733381,1575733381),
	(6,'Глютен',10,1575733381,1575733381),
	(7,'Лактоза',10,1575733381,1575733381),
	(8,'Красное мясо',10,1575733381,1575733381),
	(9,'Репчатый лук',10,1575733381,1575733381);

INSERT INTO `franchise` (`id`, `name`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'ИП Иванова (Москва)',10,1578491356,1578491356);

INSERT INTO `payment_type` (`id`, `type`, `name`, `cash_machine`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'full_pay','Оплата наличными',1,10,1571503780,1571503780),
	(2,'no_pay','Предоплата',0,10,1571503780,1571503780),
	(3,'no_pay','Оплата картой',1,10,1571503780,1571503780);

INSERT INTO `subscription` (`id`, `name`, `status`, `price`, `has_breakfast`, `has_lunch`, `has_dinner`, `has_supper`, `created_at`, `updated_at`)
VALUES
	(2,'Обед + Перекус + Ужин',10,2090,0,1,1,1,1580569150,1580569150),
	(3,'Завтрак + Обед + Перекус + Ужин',10,2290,1,1,1,1,1580569150,1580569150),
	(4,'Обед + Перекус',10,1690,0,1,1,0,1580569150,1580569150),
	(5,'Завтрак + Обед + Перекус',10,1890,1,1,1,0,1580569150,1580569150);

INSERT INTO `subscription_discount` (`id`, `subscription_id`, `count`, `price`, `created_at`, `updated_at`)
VALUES
	(20,2,5,2190,1576525484,1576525484),
	(21,2,10,2140,1576525484,1576525484),
	(22,2,20,2090,1576525484,1576525484),
	(23,3,5,10890,1578056319,1578056319),
	(24,3,10,20600,1578056319,1578056319),
	(25,3,20,38900,1578056319,1578056319),
	(26,4,5,7990,1580569058,1580569058),
	(27,4,10,15200,1580569058,1580569058),
	(28,4,20,28700,1580569058,1580569058),
	(29,5,5,8990,1580569150,1580569150),
	(30,5,10,16990,1580569150,1580569150),
	(31,5,20,32000,1580569150,1580569150);

INSERT INTO `user` (`id`, `auth_key`, `access_token`, `password_hash`, `password_reset_token`, `email`, `phone`, `fio`, `status`, `franchise_id`, `created_at`, `updated_at`)
VALUES
	(4,'JnFY7IRDWeoHvXaeinWVNLLBbv2Qjo_g','85YtOMFI02CGKyb_2I5J9_u-pWH-XKJ2','$2y$13$fVHco8exn7e3Gi4LaqoIuuwbKD3mdDe6dlHqCFYiJ23qOIJ5NhmiS','1a-CE7vV24ePYFfgCcudTZD6hUxomaPM_1571242444','sleverin@bk.ru',NULL,'Хайртдинов Шамиль',10,NULL,1571235635,1571242444);