temp-tracker
============

A simple Temperature Tracker showing off some Cassandra, MySQL, Web Service and Bootstrap work.

I recently acquired a Raspberry Pi, and wanted something to do with it. Being a database guy, the first thing I did was toss MySQL on it. So far so good.

Next I installed the rest of LAMP on it (PHP and Apache).

Now to work!

I wanted to get a set of data into the MySQL, gathered from a distant source. I found a temperature web service available taking temperature readings at LAX here

http://w1.weather.gov/xml/current_obs/KLAX.xml

So with a bit of CURL and a crontab, I was reading temps and inserting into the Pi's MySQL. The initial cron is called on my main server, which CURLs to the PI.

So far, so good.

As I've been working with Cassandra lately, I figured I'd add it into the mix. The Pi CURLs back to the main server upon completion of the MySQL insert and calls some code that does a Cassandra insert.

Finally there's a simple web 2.0 page that extracts data from both MySQL and Cassandra and displays is in grpahical form as well as a raw dump.

Some of the technologies used:

CURL
PHPCassa
Cassandra
MySQL
Bootstrap
JSON


