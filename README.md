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

- [X] Add guest users using tokens
- [X] Remove serialized in the DB
- [ ] Refactor urlparams
- [ ] INSERT wdesk_settings example data IF NOT EXIST
- [ ] Download thread as pdf, csv, and html
- [ ] Improve security
- [ ] Replace cookies by session
- [ ] Refactor frontend/script and frontend.php
- [ ] Department email
- [ ] Ticket notes
- [ ] Admin only thread inside ticket
- [ ] Use token with id
- [ ] Token expires
- [ ] Padronize variables and function names
- [ ] Remove main script from frontend
- [ ] Refactor security