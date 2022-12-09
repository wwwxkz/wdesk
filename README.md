# wdesk

Available in https://wordpress.org/plugins/wdesk

# Screenshots

![](https://github.com/wwwxkz/wdesk/blob/main/README/screenshot-1.png)
![](https://github.com/wwwxkz/wdesk/blob/main/README/screenshot-2.png)
![](https://github.com/wwwxkz/wdesk/blob/main/README/screenshot-3.png)
![](https://github.com/wwwxkz/wdesk/blob/main/README/screenshot-4.png)
![](https://github.com/wwwxkz/wdesk/blob/main/README/screenshot-5.png)
![](https://github.com/wwwxkz/wdesk/blob/main/README/screenshot-6.png)
![](https://github.com/wwwxkz/wdesk/blob/main/README/screenshot-7.png)
![](https://github.com/wwwxkz/wdesk/blob/main/README/screenshot-8.png)

# Support

### Environment

- Wordpress > 5.0 
- PHP > 7.0
- Git clone main
- New branch 'feature/fix/update feature/update-name short description'

### Structure

- wdesk
	- admin (Admin panel pages)
		- [page_name]
			- [page_name.php] (Calls nedded documents)
			- [html.php] (Logic goes here)
	- languages (Internationalization)
		- wdesk.pot (Main file to generate new internationalizations or update existing ones)
		- wdesk-pt_BR.po (.po and .mo are automatically generated using Poedit)
		- wdesk-pt_BR.mo
	- script (Backend scripts) 
		- helpers (.csv download, notify user, tiny helpers)
		- functions (Long functions with many parameters usually related to table operations "CRUD" as tickets and users)
	- shortcode (All shortcodes)
		- components (Stringified HTML of shortcodes)
			- scripts (Stringified JS of shortcodes)
			- styles (Stringified CSS of shortcodes)
			- [component_name.php]
		- [shortcode_name.php] (Call nedded components)
		shortcode.php (Registers all shortcodes)
	- index.php (Main Wordpress plugin file)
	- readme.txt (Wordpress online plugin page configuration file)

### Shortcodes

- wdesk_guest 	(Create ticket as a guest)
- wdesk_access 	(Sign-in, Log-in and Recover)
- wdesk_log_in 	(Log-in form)
- wdesk_sign_in (Sign-in form)
- wdesk_recover (Recover user password)
	
### Internationalization

- ```wp i18n make-pot . languages/wdesk.pot --slug=wdesk```
- Or use languages/wdesk.pot
- Poedit
- New branch 'add/fix/update language-name_variant short description'

### Progress

- [X] Improve prototype
	- [X] Add guest users using tokens
	- [X] Remove serialized in the DB
	- [X] Remove main script from frontend
	- [X] INSERT wdesk_settings example data IF NOT EXIST
	- [X] Replace wdesk_settings for get_option
- [X] Refactor frontend/script and frontend.php
	- [X] Padronize variables and function names
	- [X] Replace colspan by css
- [ ] Improve security
	- [X] Replace cookies by session
	- [X] Refactor urlparams
	- [X] Use token with id
		- [ ] Token expires
	- [ ] Refactor security
		- [ ] Access token instead of session password

### Features

- [ ] Usability improvement
	- [X] Download ticket as .csv
	- [X] Ticket notes
	- [X] Search and filter
	- [X] Last update
	- [X] Tags
	- [X] Department email
	- [ ] Rich text
- [X] Add more customization and tools
	- [X] Date format
	- [X] Ban emails
	- [X] Ban email providers
	- [X] Reports
	- [X] Max subject, thread
	- [X] Autoclose
