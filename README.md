# htdocs-libregraphics-projects

Php to browse the Libre Graphics projects

- The raw data is stored in a git repository: [github.com/aoloe/libregraphics-projects](https://github.com/aoloe/libregraphics-projects).
- Each project has sits in its own directory named after the project.
- Each directory has at least a markdown and a yaml file named after the project with basic informations.
  - The `.md` file contains a free form description of the project. This file is converted as html and rendered as is
  - The `.yaml` file contains structured information about the project. The information are used for displaying the key facts about the project and for searching.
  - The project can also contain images that are linked from the `.md` file. 
- The project descriptions are mirrored as `.html` files that get included in the rendered pages.
- The information from the `.yaml` file is mirrored in a database.

We need:

- A category view.
- A search form with:
  - categories
  - license
  - active
  - last released
  - tags / keywords

# Specification:

- Multiple categories per project
- Multiple licenses per project
- Multiple tags/keywords per project

## Questions

- Do we need categories if we have tags?

## Plans

- using [htdocs-gitapi-get](https://github.com/aoloe/htdocs-gitapi-get) to fetch the files from the git repository.
- using the [slim framework](http://www.slimframework.com/).
- using the [twig template engine](http://twig.sensiolabs.org).
- probably, paris and idiorm for the database access.
- look at this [pictures viewer](https://github.com/jeremykendall/flaming-archer) for inspiration
- see also [Rapid Application Prototyping in PHP Using a Micro Framework](http://net.tutsplus.com/tutorials/php/rapid-application-prototyping-in-php-using-a-micro-framework/).
- [MozMorris has a Slim fork](https://github.com/MozMorris/Slim/tree/webroot) with support for the same directory structure as i have (I took the second `.htacess` from there and convinced me to create the patch below)

## Install

- Get the latest Slim code and put it in the `Slim/` directory
  - Patch `Slim/Environment.php` to get Slim to work in a subfolder:  

            <                 $physicalPath = str_replace('\\', '', dirname($scriptName)); // <-- With rewriting
            ---
            >                 if (array_key_exists('REDIRECT_URL', $_SERVER) && ($_SERVER['REDIRECT_URL'] != $_SERVER['SCRIPT_NAME']) && (strpos(dirname($_SERVER['REDIRECT_URL']), basename($_SERVER['SCRIPT_NAME'])) == 0)) {
            >                     $physicalPath = dirname($_SERVER['REDIRECT_URL']); // <-- With rewriting to a subfolder
            >                 } else {
            >                     $physicalPath = str_replace('\\', '', dirname($scriptName)); // <-- With rewriting
            >                 }

- Get the latest Twig code and put it in the `Twig/` directory
- Get the latest Slim-Views code and put it in the new `Slim/Views` directory
- Get the latest Bootstrap package and add the `css/`, `js/` and `fonts/` files in `public/`

## Files structure

    /
        .htaccess        <-- redirect to public/index.php
        app/             <-- all the logic
            index.php
            config/
            views/
        public/          <-- the content accessible per HTTP
            index.php    <-- the entry point for all HTTP requests
            css/
            fonts/
            img/
            js/
        vendor/          <-- the external libraries
            Slim/
            Twig/
