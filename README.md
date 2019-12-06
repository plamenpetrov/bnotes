# Bnotes

Zend Framework based Customer Relationship Management System. This project was developed with collaboration of my colleague and used for small business. Core functionalities are:
* Fully contact management such as companies and persons related to them 
* Dynamically add custom form fields based on user needs
* Add and Remove Tag to contacts to easy search and find them
* Merge contacts to prevent duplication
* Import data and Export to Excel 
* Email intergation to import task from email
* Send email to reminde futured task
* Add note to contact including file and description
* Google maps integration to display contact address in contact record
* Add task to contact and calendar preview. Task can be marked as finished, missed etc.
* Projects module to easily manage tasks and contacts
* Access control level to restrict user functionalities

## Getting Started

Download files in your web server directory and extract them. Before you run application you need to have installed wamp server or any other web server pack. MySQL is also required.  

### Installing

Download files in your web server directory and extract them. Create database on MySQL and execute sql queries on /docs directory to create necessary tables. After successfully executed queries go to /application/config/database.ini and change database credentials according to your local environment.

```
adapter = "pdo_mysql"
params.host = "localhost"
params.port = "3306"
params.dbname = "YOUR_DATABASE",
params.username = "YOUR_USERNAME"
params.password = "YOUR_DATABASENAME"
isdefaulttableadapter = true
params.charset = "utf8"
```

You may need to configure and new virtual host on your web server to serve this project. After this configuration restart your server and load your project. You have to see login page where you can enter this credentials for admin access:
* username: admin
* password: test
 or for regular access:
 * username: demo
 * password: test

## Deployment

Add additional notes about how to deploy this on a live system

## Built With

* [Zend Framework](https://framework.zend.com/manual/1.12/en/learning.quickstart.html)
* [Wamp](http://www.wampserver.com/en/) - Web development environment

## Authors

* **Plamen Petrov** - [PlamenPetrov](https://github.com/plamenpetrov)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
