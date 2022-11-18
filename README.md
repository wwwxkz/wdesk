# wdesk

# Support

### Environment

1 - Wordpress > 5.0 PHP > 7.0
2 - Git clone main
3 - New branch 'feature/fix/update feature/update-name short description'

### Internationalization

0 - ```wp i18n make-pot . languages/wdesk.pot --slug=wdesk```
1 - Or use languages/wdesk.pot
2 - Poedit
3 - New branch 'add/fix/update language-name_variant short description'

### Progress

- [X] Improve prototype
	- [X] Add guest users using tokens
	- [X] Remove serialized in the DB
	- [X] Remove main script from frontend
	- [X] INSERT wdesk_settings example data IF NOT EXIST
- [ ] Refactor frontend/script and frontend.php
	- [X] Padronize variables and function names
	- [X] Replace colspan by css
- [ ] Improve security
	- [X] Replace cookies by session
	- [X] Refactor urlparams
	- [X] Use token with id
		- [ ] Token expires
	- [ ] Refactor security

### Features

- [ ] Usability improvement
	- [X] Download ticket as .csv
	- [X] Ticket notes
	- [ ] Search and filter
	- [X] Last update
	- [ ] Tags
	- [ ] Department email
	- [ ] Due date
	- [ ] Time worked
	- [ ] Rich text
	- [ ] Due date notify
- [ ] Add more customization and tools
	- [ ] Date format
	- [ ] Ban emails
	- [ ] Ban email providers
	- [ ] Reports
	- [ ] Darkmode
	- [ ] Max text, subject, file
	- [ ] Fields in ticket creation
	- [ ] Cron status
	- [ ] Autoclose
	- [ ] Color scheme
