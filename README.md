# Student Payout

This is PHP app that works out the students allowances based on various criteria
such as age, attendance, distance etc.

First clone the repo on your local machine like
so: `git clone git@github.com:Shemsedin/student-payout.git`

## Running the App

## Using Docker

To build and run on Docker container you would need to
have [docker](https://www.docker.com/) installed in your machine.

As I am using composer for this application first thing to do is open the
terminal and then navigate to the root directory of the application and then
run:

`composer install`

This will install the project dependencies, in this case we are only using
autoloader.

## Building and Running on Docker Container

Once the above is done, while in the root directory do the following

1) Build and run
   `docker-compose up -d`
2) Using your favorite browser navigate to
   `http://localhost:8080/`

There you will see the students id and their total payout.

To stop the application run this:

`docker-compose down`

## Without Docker

You can also run the app without Docker to do that open the terminal navigate to
the root directory of this application and then install the dependencies using
composer as described above and then run `php -S localhost:8081` this will run
on port 8081, you can change the port to whatever you like








