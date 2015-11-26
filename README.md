Placing Bad - Image Placeholder
===============================

![alt tag](https://github.com/DesignyourCode/placing-bad/blob/master/public_html/assets/img/bg.jpg?raw=true)

Who wouldn't want a Breaking Bad placeholder generator...and as developers and huge Breaking Bad fans...we decided to build one. After discussions with a couple of other devs, I started to put together a site to do it.
With support and input from some of the other guys (listed in the footer of the site). This is the end result.

So welcome to 'Placing Bad'. With this placeholder service you can cook up a site to the clients exact (blue)prints...see what I did there? ;)

Improvements
------------

We accept changes to the site, so if you feel the service doesn't quite offer everything you want, you can create a ticket and we will see what we can do.

Of course you are welcome to contribute too? Just fork a copy, make your changes and submit a merge request. We will review it and marge it in if we like it.


Requirements & Installation
---------------------------

1. Run 'composer install'. This will install:
    - <a href="http://www.slimframework.com/" target="_blank">Slim PHP Framework</a>
    - <a href="http://twig.sensiolabs.org/" target="_blank">Twig PHP Templating Engine</a>
    - <a href="https://github.com/claviska/SimpleImage" target="_blank">SimpleImage PHP Class</a>

2. Set your localhost to use public_html as the root

Parameters and Usage
--------------------

Placing Bad now supports url parameters for customisation your image request.

Available options with their default values are:

`desaturate => 100`
`blur => 10`
`brightness => 50`
`color => FF0000`
`pixelate => 8`
`sepia`

To use a parameter add it to the image url like so:

```
http://placingbad.com/800/420?desaturate
```

You can also set an option for most of the parameters. This is done like so:

```
http://placingbad.com/800/420?desaturate=10
```

You can also chain options together if you would like to customise it more:

```
http://placingbad.com/800/420?desaturate=10&blur&color=d2d2d2
```

#### Other available options include:

'Random' will allow you to set a size and then every time you request the image, it will change.

```
http://placingbad.com/800/420?random
```
