### Leads Application

This application allows users to purchase sales leads. Roles-based authentication allows 
administrators to manage users (add/delete/edit). The application consists of the following 
interfaces:

- Login Interface with user authentication.
- Password change interface.
- Administrator interface for managing users.
- Interface for purchasing new leads.
- List of previously purchased leads with all details.

The application is written in PHP using a mySQL database as the data layer. It implements OOP and 
exception handling throughout. External documentation for all classes and methods can be found 
in the 'docs' folder.

## Deployment

In order to deploy the app, you'll need to make a few simple changes to the configuration files.

1. The '/include/db.inc.php' needs your actual database info.
2. The $sitefolder variable in /include/config.inc.php' needs to be set to whatever folder you 
actually place the app in relative to your root public_www directory.

## Additional Help

For additional help, contact me at bonds0097@gmail.com