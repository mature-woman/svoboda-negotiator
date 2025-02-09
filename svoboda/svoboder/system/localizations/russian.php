<?php

// Exit (success)
return [
	// Система
	'svoboda' => 'Свобода',
	'empty' => 'Пусто',

	// Главное меню
	'menu_title' => 'Главное меню',
	'menu_accounts' => 'Аккаунты',
	'menu_members' => 'Участники',
	'menu_distributions' => 'Дистрибутивы',
	'menu_button_site' => 'Сайт',
	'menu_button_map' => 'Карта',
	'menu_button_blog' => 'Блог',
	'menu_button_projects' => 'Проекты',
	'menu_button_members' => 'Участники',
	'menu_button_distributions' => 'Дистрибутивы',
	'menu_button_volunteering' => 'Стать волонтёром',
	'menu_button_message' => 'Отправить сообщение',

	// Дистрибутивы
	'distributions_title' => 'Реестр дистрибутивов',
	'distributions_description' => '*Дистрибутив* \- это автономная ячейка Свободы, являющаяся любым видом формирования \(например: коммуна\), но обязательно наследующая *Vhod* протокол, участвующая в глобальных процессах организации и привязанная к местоположению',
	'distributions_registered' => 'Зарегистрировано',
	'distributions_confirmed' => 'Подтверждено',
	'distributions_button_search' => 'Поиск',
	'distributions_button_register' => 'Зарегистрировать',

	// Настройки языка
	'settings_select_language_title' => 'Выбери язык',
	'settings_select_language_description' => 'Выбранный язык будет записан в настройки аккаунта',
	'settings_language_update_success' => 'Язык заменён:',
	'settings_language_update_fail' => 'Не удалось заменить язык',

	// Репозиторий
	'repository_title' => 'Репозиторий',
	'repository_text' => <<<TXT
	Svoboder написан на [PHP](https://www.php.net/) используя [Zanzara](https://github.com/badfarm/zanzara) для Telegram,
	мой [MINIMAL](https://git.svoboda.works/mirzaev/minimal) фреймворк для PHP и моя база данных [Baza](https://git.svoboda.works/mirzaev/baza)

	Код находится под лицензией [WTFPL](https://en.wikipedia.org/wiki/WTFPL)
	Помогай с разработкой или используй мой код бесплатно\!
	TXT,
	'repository_button_code' => 'Код',
	'repository_button_issues' => 'Проблемы',
	'repository_button_suggestions' => 'Предложения',

	// Автор
	'author_title' => 'Автор',
	'author_text' => <<<TXT
	*Арсен Мирзаев Татьяно\-Мурадович*
	Программист, анархист, вегетарианец
	TXT,
	'author_button_neurojournal' => 'Нейрожурнал',
	'author_button_projects' => 'Проекты',
	'author_button_twitter' => 'Twitter',
	'author_button_bluesky' => 'Bluesky',
	'author_button_bastyon' => 'Bastyon',
	'author_button_youtube_english' => 'YouTube',
	'author_button_youtube_russian' => 'YouTube',
	'author_button_message' => 'Отправить сообщение',

	// Выбор языка
	'select_language_title' => 'Выбери язык',
	'select_language_description' => 'Выбранный язык будет использован в текущем процессе',
	'select_language_button_add' => 'Добавить язык',

	// Выбор дистрибутива
	'select_distributions_title' => 'Выбери дистрибутив',
	'select_distributions_description' => 'Выбранный дистрибутив будет использован в текущем процесса',
	'select_distribution_button_registrate' => 'Зарегистрировать дистрибутив',

	// Регистрация дистрибутива
	'distribution_registration_started' => 'Процесс регистрации дистрибутива запущен',
	'distribution_registration_not_started' => 'Процесс регистрации дистрибутива не запущен',
	'distribution_registration_continiued' => 'Процесс регистрации дистрибутива найден и продолжен',
	'distribution_registration_generation' => 'Генерация записи дистрибутива',
	'distribution_registration_created_distribution' => 'Создана запись дистрибутива в базе данных',
	'distribution_registration_created_localization' => 'Создана запись локализации дистрибутива в базе данных',
	'distribution_registration_canceled' => 'Процесс регистрации дистрибутива отменён',
	'distribution_registration_completed' => 'Процесс регистрации дистрибутива завершен',
	'distribution_registration_not_created_distribution' => 'Не удалось создать запись дистрибутива в базе данных',
	'distribution_registration_not_created_localization' => 'Не удалось создать запись локализации дистрибутива в базе данных',
	'distribution_registration_button_language' => 'Язык',
	'distribution_registration_select_language_title' => 'Выбери язык',
	'distribution_registration_select_language_description' => "Выбранный язык позволит создать локализацию для пользователей с таким же языком\n\nТы можешь создать 1 локализацию для каждого языка",
	'distribution_registration_language_update_success' => 'Язык заменён:',
	'distribution_registration_language_update_fail' => 'Не удалось заменить язык',
	'distribution_registration_button_name' => 'Название',
	'distribution_registration_name_request' => 'Введи название',
	'distribution_registration_name_request_not_acceptable' => 'Не удалось обработать название',
	'distribution_registration_name_request_too_short' => 'Длина имени должна быть \>\= 3 и \<\= 32',
	'distribution_registration_name_request_too_long' => 'Длина имени должна быть \>\= 3 и \<\= 32',
	'distribution_registration_name_request_spaces' => "Разрешено использовать не более чем 2 пробела",
	'distribution_registration_name_request_restricted_characters_title' => "Запрещены любые символы кроме букв",
	'distribution_registration_name_request_restricted_characters_description' => "Удали эти символы:",
	'distribution_registration_name_update_success' => 'Название заменено:',
	'distribution_registration_name_update_fail' => 'Не удалось заменить название',
	'distribution_registration_button_location' => 'Местоположение',
	'distribution_registration_button_location_send' => 'Отправить местоположение',
	'distribution_registration_location_send_title' => 'Отправь местоположение',
	'distribution_registration_location_send_description' => "У тебя появилась кнопка на основной клавиатуре\nПри нажатии на неё можно будет выбрать локацию на карте\n\n*Пришли широту и долготу в формате:* 50\.969043, 9\.797588",
	'distribution_registration_location_send_not_acceptable' => 'Не удалось обработать местоположение',
	'distribution_registration_location_send_latitude_too_small' => 'Широта должна быть \>\= 0 и \<\=90',
	'distribution_registration_location_send_latitude_too_big' => 'Широта должна быть \>\= 0 и \<\=90',
	'distribution_registration_location_send_longitude_too_small' => 'Долгота должна быть \>\= 0 и \<\=180',
	'distribution_registration_location_send_longitude_too_big' => 'Долгота должна быть \>\= 0 и \<\=180',
	'distribution_registration_location_update_success' => 'Местоположение заменено:',
	'distribution_registration_location_update_fail' => 'Не удалось заменить местоположение',
	'distribution_registration_button_confirm' => 'Подтвердить',
	'distribution_registration_button_cancel' => 'Отменить',

	// Локализация дистрибутива
	'distribution_localization_started' => 'Запущен процесс локализации дистрибутива',
	'distribution_localization_continiued' => 'Найден и продолжен процесс локализации дистрибутива',
	'distribution_localization_created' => 'Создана запись локализации дистрибутива в базе данных',
	'distribution_localization_not_created' => 'Не удалось создать запись локализации дистрибутива в базе данных',
	'distribution_localization_select_language_title' => 'Выбери язык',
	'distribution_localization_select_language_description' => "Выбранный язык позволит создать локализацию для пользователей с таким же языком\n\nТы можешь создать 1 локализацию для каждого языка",

	// Поиск дистрибутива
	'distribution_search_started' => 'Процесс поиска дистрибутива запущен',
	'distribution_search_not_started' => 'Процесс поиска дистрибутива не запущен',
	'distribution_search_ended' => 'Процесс поиска дистрибутива завершён',
	'distribution_search_not_localized' => 'Не удалось инициализировать локализацию дистрибутива',
	'distribution_search_not_named' => 'Нет названия',
	'distribution_search_continiued' => 'Процесс поиска дистрибутива найден и продолжен',
	'distribution_search_empty' => 'Не найдены дистрибутивы',
	'distribution_search_title' => 'Поиск дистрибутива',
	'distribution_search_members' => 'Участники',
	'distribution_search_location' => 'Местоположение',
	'distribution_search_page_next_exists' => 'В реестре есть ещё дистрибутивы',
	'distribution_search_page_next_not_exists' => 'В реестре больше нет дистрибутивов',
	'distribution_search_button_text' => 'Текст',
	'distribution_search_text_request_title' => 'Введи текст для поиска',
	'distribution_search_text_request_description' => 'Поиск будет проводиться по названиям с использованием функции Левенштейна',
	'distribution_search_text_request_not_acceptable' => 'Не удалось обработать поисковый текст',
	'distribution_search_text_request_too_short' => 'Длина поскового текста должна быть \>\= 3 и \<\= 64',
	'distribution_search_text_request_too_long' => 'Длина поискового текста должна быть \>\= 3 и \<\= 64',
	'distribution_search_text_request_restricted_characters_title' => "Запрещены любые символы кроме букв",
	'distribution_search_text_request_restricted_characters_description' => "Удали эти символы:",
	'distribution_search_text_update_success' => 'Поисковый текст заменён:',
	'distribution_search_text_update_fail' => 'Не удалось заменить поисковый текст',
	'distribution_search_button_location' => 'Местоположение',
	'distribution_search_button_location_send' => 'Отправить местоположение',
	'distribution_search_location_send_title' => 'Отправь местоположение',
	'distribution_search_location_send_description' => "У тебя появилась кнопка на основной клавиатуре\nПри нажатии на неё можно будет выбрать локацию на карте\n\n*Пришли широту и долготу в формате:* 50\.969043, 9\.797588",
	'distribution_search_location_send_not_acceptable' => 'Не удалось обработать местоположение',
	'distribution_search_location_send_latitude_too_small' => 'Широта должна быть \>\= 0 и \<\=90',
	'distribution_search_location_send_latitude_too_big' => 'Широта должна быть \>\= 0 и \<\=90',
	'distribution_search_location_send_longitude_too_small' => 'Долгота должна быть \>\= 0 и \<\=180',
	'distribution_search_location_send_longitude_too_big' => 'Долгота должна быть \>\= 0 и \<\=180',
	'distribution_search_location_update_success' => 'Местоположение заменено:',
	'distribution_search_location_update_fail' => 'Не удалось заменить местоположение',
	'distribution_search_button_distance' => 'Расстояние',
	'distribution_search_distance_request_title' => 'Введи расстояние',
	'distribution_search_distance_request_description' => 'Поиск будет производиться в радиусе от этого значения по формуле Винсенти',
	'distribution_search_distance_request_not_acceptable' => 'Не удалось обработать расстояние',
	'distribution_search_distance_request_too_short_km' => 'Длина расстояния должна быть \>\= 0 и \<\= 600',
	'distribution_search_distance_request_too_long_km' => 'Длина расстояния должна быть \>\= 0 и \<\= 600',
	'distribution_search_distance_request_restricted_characters_title' => "Запрещены любые символы кроме цифр",
	'distribution_search_distance_request_restricted_characters_description' => "Удали эти символы:",
	'distribution_search_distance_update_success' => 'Расстояние заменено:',
	'distribution_search_distance_update_fail' => 'Не удалось заменить расстояние',
	'distribution_search_button_confirmed' => 'Подтверждённые',
	'distribution_search_button_confirmed_all' => 'Все',
	'distribution_search_confirmed_update_success' => 'Фильтр по статусу заменён:',
	'distribution_search_confirmed_update_fail' => 'Не удалось заменить фильтр по статусу',
	'distribution_search_button_start' => 'Начать поиск',
	'distribution_search_button_end' => 'Завершить поиск',
	'distribution_search_button_page_next' => 'Следующая страница',
	'distribution_search_button_map' => 'Карта',
	'distribution_search_button_members' => 'Участники',
	'distribution_search_button_message' => 'Отправить сообщение',
	'distribution_search_km' => 'км',
	'distribution_search_mi' => 'мл',

	// Авторизация
	'not_authorized_system' => 'У тебя нет доступа к системе',
	'not_authorized_contact' => 'У тебя нет доступа к коммуникации с организацией',
	'not_authorized_request' => 'У тебя нет доступа к отправке запросов в организацию',
	'not_authorized_settings' => 'У тебя нет доступа к настройкам',
	'not_authorized_system_settings' => 'У тебя нет доступа к системным настройкам',

	// Прочее
	'why_so_shroomious' => 'почему такой грибъёзный',
];
