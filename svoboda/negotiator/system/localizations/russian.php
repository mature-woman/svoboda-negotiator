<?php

// Exit (success)
return [
	// Система
	'svoboda' => 'Свобода',
	'empty' => 'Пусто',

	// Выбор языка
	'select_language_title' => 'Выбери язык',
	'select_language_description' => 'Выбранный язык будет использован в текущем процессе',
	'select_language_button_add' => 'Добавить язык',

	// Выбор дистрибутива
	'select_distributions_title' => 'Выбери дистрибутив',
	'select_distributions_description' => 'Выбранный дистрибутив будет использован в текущем процесса',
	'select_distribution_button_registrate' => 'Зарегистрировать дистрибутив',

	// Настройки
	'settings_select_language_title' => 'Выбери язык',
	'settings_select_language_description' => 'Выбранный язык будет записан в настройки аккаунта',
	'settings_language_update_success' => 'Язык заменён:',
	'settings_language_update_fail' => 'Не удалось заменить язык',

	// Дистрибутивы
	'distributions_title' => 'Реестр дистрибутивов',
	'distributions_description' => '*Дистрибутив* \- это автономная ячейка Свободы, являющаяся любым видом формирования \(например: коммуна\), но обязательно наследующая *Vhod* протокол, участвующая в глобальных процессах организации и привязанная к местоположению',
	'distributions_registered' => 'Зарегистрировано',
	'distributions_button_search' => 'Поиск',
	'distributions_button_register' => 'Зарегистрировать',

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
	'distribution_registration_name_request' => 'Введите название',
	'distribution_registration_name_request_not_acceptable' => 'Не удалось обработать название',
	'distribution_registration_name_request_too_short' => 'Длина имени должна быть \>\= 3 и \<\= 32',
	'distribution_registration_name_request_too_long' => 'Длина имени должна быть \>\= 3 и \<\= 32',
	'distribution_registration_name_request_spaces' => "Разрешено использовать не более чем 2 пробела",
	'distribution_registration_name_request_restricted_characters_title' => "Запрещены любые символы кроме букв",
	'distribution_registration_name_request_restricted_characters_description' => "Удалите эти символы:",
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

	// Авторизация
	'not_authorized_system' => 'У тебя нет доступа к системе',
	'not_authorized_contact' => 'У тебя нет доступа к коммуникации с организацией',
	'not_authorized_request' => 'У тебя нет доступа к отправке запросов в организацию',
	'not_authorized_settings' => 'У тебя нет доступа к настройкам',
	'not_authorized_system_settings' => 'У тебя нет доступа к системным настройкам',

	// Прочее
	'why_so_shroomious' => 'почему такой грибъёзный',
];
