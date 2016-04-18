Working with this project:<br />
1. Clone this repo to your local machine.<br />
2. Create a config.php file at the root of the project.<br />
3. Create a MySQL database on your local machine, and import the .sql dump found in 'extras'.<br />
4. Inside the config.php, use the following:<br />

`$Config = array(
     "mw2" => array(
         "host" => "localhost",
         "username" => "test",
         "password" => "db",
         "database" => "test"
     )
 );`

replacing the parameters with the credentials that correspond to the database you set up in 3.<br />
5. Head to your (localhost) address in your browser and done!

TO-DO LIST

1. Content
	- users pages
	- other pages
	- records*
	- total bodyhits*

2. Aspect
	- fix small CSS issues
	- make a fancy header (keep it fixed or not)
	- complete the homepage

3. Features
	- add "interactivity" with js/ajax calls
	- table orders
	- graphs in small pages* (users/servers/bodyhits)
	- signature generator*
	
* optional points