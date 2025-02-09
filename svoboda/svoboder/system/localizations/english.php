<?php

// Exit (success)
return [
	// System
	'svoboda' => 'Svoboda',
	'empty' => 'Empty',

	// Main menu
	'menu_title' => 'Main menu',
	'menu_accounts' => 'Accounts',
	'menu_members' => 'Members',
	'menu_distributions' => 'Distributions',
	'menu_button_site' => 'Site',
	'menu_button_map' => 'Map',
	'menu_button_blog' => 'Blog',
	'menu_button_projects' => 'Projects',
	'menu_button_members' => 'Members',
	'menu_button_distributions' => 'Distributions',
	'menu_button_volunteering' => 'Become a volunteer',
	'menu_button_message' => 'Send a message',

	// Distributions
	'distributions_title' => 'Registry of distributions',
	'distributions_description' => '*Distribution* is an autonomous cell of Svoboda, representing any type of formation \(for example: commune\), but necessarily inherits the *Vhod* protocol and tied to a location',
	'distributions_registered' => 'Registered',
	'distributions_confirmed' => 'Confirmed',
	'distributions_button_search' => 'Search',
	'distributions_button_register' => 'Registrate',

	// Language setting
	'settings_select_language_title' => 'Select language',
	'settings_select_language_description' => 'The selected language will be writed in your account settings',
	'settings_language_update_success' => 'Language replaced:',
	'settings_language_update_fail' => 'Failed to replace language',

	// Repository
	'repository_title' => 'Repository',
	'repository_text' => <<<TXT
	Svoboder is written in [PHP](https://www.php.net/) using [Zanzara](https://github.com/badfarm/zanzara) for Telegram,
	my [MINIMAL](https://git.svoboda.works/mirzaev/minimal) framework for PHP and my [Baza](https://git.svoboda.works/mirzaev/baza) database

	The code is under the [WTFPL](https://en.wikipedia.org/wiki/WTFPL) license
	You can help me with the development, or use my code for free\!
	TXT,
	'repository_button_code' => 'The code',
	'repository_button_issues' => 'Issues',
	'repository_button_suggestions' => 'Suggestions',

	// Author
	'author_title' => 'Author',
	'author_text' => <<<TXT
	*Arsen Mirzaev Tatyano\-Muradovich*
	Programmer, anarchist, vegetarian
	TXT,
	'author_button_neurojournal' => 'Neurojournal',
	'author_button_projects' => 'Projects',
	'author_button_twitter' => 'Twitter',
	'author_button_bluesky' => 'Bluesky',
	'author_button_bastyon' => 'Bastyon',
	'author_button_youtube_english' => 'YouTube',
	'author_button_youtube_russian' => 'YouTube',
	'author_button_message' => 'Send a message',

	// Language selection
	'select_language_title' => 'Select language',
	'select_language_description' => 'The selected language will be used in the current process',
	'select_language_button_add' => 'Add a language',

	// Distribution selection
	'select_distributions_title' => 'Select distribution',
	'select_distributions_description' => 'The selected distribution will be used in the current process',
	'select_distribution_button_registrate' => 'Registrate a distribution',

	// Distribution registration
	'distribution_registration_started' => 'Process of the distribution registration started',
	'distribution_registration_not_started' => 'Process of the distribution registration has not started',
	'distribution_search_ended' => 'Process of the distribution registration ended',
	'distribution_registration_continiued' => 'Process of the distribution registration found and continiued',
	'distribution_registration_generation' => 'The distribution record generation',
	'distribution_registration_created_distribution' => 'Created the distribution record in the database',
	'distribution_registration_created_localization' => 'Created the distribution localization record in the database',
	'distribution_registration_canceled' => 'Process of the distribution registration canceled',
	'distribution_registration_completed' => 'Process of the distribution registration completed',
	'distribution_registration_not_created_distribution' => 'Failed to create the distribution record in the database',
	'distribution_registration_not_created_localization' => 'Failed to create the distribution localization record in the database',
	'distribution_registration_button_language' => 'Language',
	'distribution_registration_select_language_title' => 'Choose language',
	'distribution_registration_select_language_description' => "The selected language will create a localization for users with the same language\n\nYou can create 1 localization for each language",
	'distribution_registration_language_update_success' => 'Language replaced:',
	'distribution_registration_language_update_fail' => 'Failed to replace language',
	'distribution_registration_button_name' => 'Name',
	'distribution_registration_name_request' => 'Enter name',
	'distribution_registration_name_request_not_acceptable' => 'Failed to process the name',
	'distribution_registration_name_request_too_short' => 'Name length must be \>\= 3 and \<\= 32',
	'distribution_registration_name_request_too_long' => 'Name length must be \>\= 3 and \<\= 32',
	'distribution_registration_name_request_spaces' => "No more than 2 spaces are allowed",
	'distribution_registration_name_request_restricted_characters_title' => "Restricted any characters except letters",
	'distribution_registration_name_request_restricted_characters_description' => "Remove these characters:",
	'distribution_registration_name_update_success' => 'Name replaced:',
	'distribution_registration_name_update_fail' => 'Failed to replace name',
	'distribution_registration_button_location' => 'Location',
	'distribution_registration_button_location_send' => 'Send location',
	'distribution_registration_location_send_title' => 'Send location',
	'distribution_registration_location_send_description' => "You now have a button on your main keyboard\nWhen you click on it, you can select a location on the map\n\n*Send latitude and longitude in the format:* 50\.969043, 9\.797588",
	'distribution_registration_location_send_not_acceptable' => 'Failed to process the location',
	'distribution_registration_location_send_latitude_too_small' => 'Latitude must be \>\= 0 and \<\=90',
	'distribution_registration_location_send_latitude_too_big' => 'Latitude must be \>\= 0 and \<\=90',
	'distribution_registration_location_send_longitude_too_small' => 'Longitude must be \>\= 0 and \<\=180',
	'distribution_registration_location_send_longitude_too_big' => 'Longitude must be \>\= 0 and \<\=180',
	'distribution_registration_location_update_success' => 'Location replaced:',
	'distribution_registration_location_update_fail' => 'Failed to replace location',
	'distribution_registration_button_confirm' => 'Confirm',
	'distribution_registration_button_cancel' => 'Cancel',

	// Distribution localization
	'distribution_localization_started' => 'Registration of the distribution process started',
	'distribution_localization_continiued' => 'Registration of the distribution process found and continiued',
	'distribution_localization_created' => 'Created the distribution localization record in the database',
	'distribution_localization_not_created' => 'Failed to create the distribution localization record in the database',
	'distribution_localization_select_language_title' => 'Choose language',
	'distribution_localization_select_language_description' => "The selected language will create a localization for users with the same language\n\nYou can create 1 localization for each language",

	// Distribution search
	'distribution_search_started' => 'Process of the distribution search started',
	'distribution_search_not_started' => 'Process of the distribution search has not started',
	'distribution_search_not_localized' => 'Failed to initialize the distribution localization',
	'distribution_search_not_named' => 'No name',
	'distribution_search_continiued' => 'Process of the distribution search found and continiued',
	'distribution_search_empty' => 'No distributions found',
	'distribution_search_title' => 'Distribution search',
	'distribution_search_members' => 'Members',
	'distribution_search_location' => 'Location',
	'distribution_search_page_next_exists' => 'There are more distributions in the registry',
	'distribution_search_page_next_not_exists' => 'There are no more distributions in the registry',
	'distribution_search_button_text' => 'Text',
	'distribution_search_text_request_title' => 'Enter search text',
	'distribution_search_text_request_description' => 'Search will be conducted by names using the Levenshtein function',
	'distribution_search_text_request_not_acceptable' => 'Failed to process the search text',
	'distribution_search_text_request_too_short' => 'Search text length must be \>\= 3 and \<\= 64',
	'distribution_search_text_request_too_long' => 'Search text length must be \>\= 3 and \<\= 64',
	'distribution_search_text_request_restricted_characters_title' => "Restricted any characters except letters",
	'distribution_search_text_request_restricted_characters_description' => "Remove these characters:",
	'distribution_search_text_update_success' => 'Search text replaced:',
	'distribution_search_text_update_fail' => 'Failed to replace search text',
	'distribution_search_button_location' => 'Location',
	'distribution_search_button_location_send' => 'Send location',
	'distribution_search_location_send_title' => 'Send location',
	'distribution_search_location_send_description' => "You now have a button on your main keyboard\nWhen you click on it, you can select a location on the map\n\n*Send latitude and longitude in the format:* 50\.969043, 9\.797588",
	'distribution_search_location_send_not_acceptable' => 'Failed to process the location',
	'distribution_search_location_send_latitude_too_small' => 'Latitude must be \>\= 0 and \<\=90',
	'distribution_search_location_send_latitude_too_big' => 'Latitude must be \>\= 0 and \<\=90',
	'distribution_search_location_send_longitude_too_small' => 'Longitude must be \>\= 0 and \<\=180',
	'distribution_search_location_send_longitude_too_big' => 'Longitude must be \>\= 0 and \<\=180',
	'distribution_search_location_update_success' => 'Location replaced:',
	'distribution_search_location_update_fail' => 'Failed to replace location',
	'distribution_search_button_distance' => 'Distance',
	'distribution_search_distance_request_title' => 'Enter distance',
	'distribution_search_distance_request_description' => 'Search will be performed within a radius of this value using the Vincenty formula',
	'distribution_search_distance_request_not_acceptable' => 'Failed to process the distance',
	'distribution_search_distance_request_too_short_km' => 'Distance value must be \>\= 0 and \<\= 600',
	'distribution_search_distance_request_too_long_km' => 'Distance value must be \>\= 0 and \<\= 600',
	'distribution_search_distance_request_restricted_characters_title' => "Restricted any characters except digitals",
	'distribution_search_distance_request_restricted_characters_description' => "Delete these characters:",
	'distribution_search_distance_update_success' => 'Distance replaced:',
	'distribution_search_distance_update_fail' => 'Failed to replace distance',
	'distribution_search_button_confirmed' => 'Confirmed',
	'distribution_search_button_confirmed_all' => 'All',
	'distribution_search_confirmed_update_success' => 'Filter by status replaced:',
	'distribution_search_confirmed_update_fail' => 'Failed to replace filter by status',
	'distribution_search_button_start' => 'Start the search',
	'distribution_search_button_end' => 'End the search',
	'distribution_search_button_page_next' => 'Next page',
	'distribution_search_button_map' => 'Map',
	'distribution_search_button_members' => 'Members',
	'distribution_search_button_message' => 'Send a message',
	'distribution_search_km' => 'km',
	'distribution_search_mi' => 'ml',

	// Authorization
	'not_authorized_system' => 'You do not have access to the system',
	'not_authorized_contact' => 'You do not have access to contact with the organisation',
	'not_authorized_request' => 'You do not have access to requesting to the organisation',
	'not_authorized_settings' => 'You do not have access to the settings',
	'not_authorized_system_settings' => 'You do not have access to the system settings',

	// Other
	'why_so_shroomious' => 'why so shroomious',
];
