# AUTHENTICATOR

During my training at BeCode.org, we received an exercise in which the objective was to create a simple authentication system. Being comfortable with php and object-oriented programming, I started creating a class to manage application authentication as simply as possible.

## REQUIREMENTS

- PHP >= 7.2
- MYSQL or MARIADB

## USAGE

> 🚨 Before using this class, ensure you have created the required table on your database..

```SQL
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;
```

### 1. INSTALL

Before using the class, you must first import it into your script, to do this, download and move the file from the src directory to the root of your project.

Then import the file into your script as below:

```php
require_once __DIR__ . '/authenticator.php';
```

### 2. INSTANTIATE

Once the installation is complete, you can instantiate the class. It's very simple..

> Here are the parameters used to instantiate the class
>* **Hostname**: *The host name of your database server*
>* **Name**: *The name of your database*
>* **Username**: *The username used to connect to your database*
>* **Password**: *The password used to connect to your database*
>* **Port**: *The port of your database server*

```php
$auth = new Authenticator('HOSTNAME', 'NAME', 'USERNAME', 'PASSWORD', 3306);
```
👌 At this point, you are ready to make full use of the class.

### 3. METHODS

This class contains different methods to facilitate its use, you can refer to the [documentation](src/authenticator.php) in the class to familiarize yourself with it

### 4. EXAMPLES

To see examples of use, please refer to the [example file](example.php) provided in this repository.

## Authors

* [**Fouyon Joshua**](https://github.com/fouyonjoshua) - *Initial work* 

See also the list of [contributors](https://github.com/fouyonjoshua/authenticator/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details