php-framework
===

# About

php-framework is a simple php framework to write web-apps. php-framework is using HamlPHP to use Haml as template language.

# Build in
- MVC (Model, View, Controller)
- Scaffolding with command line tools
- Migrations for DB
- Own Template Engine
- HamlPHP
- Gulp
- Sass
- FontAwesome
- jQuery
- Bootstrap

# Installation
Clone the repository and go to php-framework. Now execute:

    cd console
    ./setup

Set the document root directory of your web server to:

    php-framework/public/

Create a mysql database with your favorite tool and configure your database connection in:

    php-framework/config

# Getting started
## Basis Structure

php-framework is a MVC framework. You build your web-app in:

    php-framework/app/

Your controllers are in:

    php-framework/app/controller/

There is already a example controller *index_public.php*. So you can open your root directory in a web-browser. Try *localhost* in your browser.

## Creating our first Model - "User"

First we want to create a User model to store data of a user. User can store a first name and a last name.

Got to the directory:

    php-framework/console/

Now execute:

    ./model create User firstName:string lastName:string

Now we have to migrate the user into our database:

    ./migrate -m User

## Creating a controller for "User"

To access the User by browser, we need a simple default controller. We can create this controller with:

    ./model create-controller User

Now you can open the web-page *"localhost/index.php?module=users"* Don't forget the 's'.
