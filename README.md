TicketMap:round_pushpin:
=========

Intelligently map the location of tickets in an SQL database

  - Intelligent coordinate caching
  - Responsive design
  - Lightweight server footprint

This project was originally made with the database stucture of TigerPaw 2011 on Microsoft SQL Server in mind, but can be easily adapted to the schema of any other database.

History
---

This project was written in **2012** as my first foray into PHP and Javascript, please excuse the mess!

Tech
---

TicketMap uses a number of open source projects to work properly:

* [PHP5] - oldie but a goodie! used for dynamic web content
* [Python2] - scripting language for the *best* of us
* [GeoPy] - python geocoding toolkit
* [Microsoft SQL] - yet another closed SQL implementation
* [Bootstrap] - great UI boilerplate for modern web apps
* [jQuery] - used for its glorious JSON features among other things
* [Google Maps API] - beautiful mapping frontend
* [MapQuest Geocoding API] - geocoding without hard limits!

Installation
---

This code was published in order to be modified to fit *your* needs! That said, you'll need the following programs installed for this project to run properly.

* Web Server - I use [Nginx]
* SQL Server - I use [Microsoft SQL]
* [PHP5] ≥ 5.4
* [Python2] ≥ 2.7.0
* [GeoPy] ≥ 1.0

License
---

**MIT**

Do whatever you want with this!


[PHP5]:http://php.net/
[Python2]:https://www.python.org/
[GeoPy]:http://geopy.readthedocs.org/en/latest/
[Microsoft SQL]:http://www.microsoft.com/sql
[Bootstrap]:http://twitter.github.com/bootstrap/
[jQuery]:http://jquery.com
[Google Maps API]:https://developers.google.com/maps/documentation/javascript/
[MapQuest Geocoding API]:http://developer.mapquest.com/web/products/dev-services/geocoding-ws
[Nginx]:http://nginx.org/