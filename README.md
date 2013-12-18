# htdocs-libregraphics-projects

Php to browse the Libre Graphics projects

- The raw data is stored in a git repository: [github.com/aoloe/libregraphics-projects](https://github.com/aoloe/libregraphics-projects).
- Each project has sits in its own directory named after the project.
  - Each directory has at least a markdown and a yaml file named after the project with basic informations.
     - The `.md` file contains a free form description of the project. This file is converted as html and rendered as is
     - The `.yaml` file contains structured information about the project. The information are used for displaying the key facts about the project and for searching.
     - The project can also contain images that are linked from the `.md` file. 
- The project descriptions are mirrored as `.html` files that get included in the rendered pages.
- The information from the `.yaml` file is mirrored in a database

## Plans

- using the [slim framework](http://www.slimframework.com/)
- using the [twik template engine](twig.sensiolabs.org)
- probably, paris and idiorm for the database access.
- look at this [pictures viewer](https://github.com/jeremykendall/flaming-archer) for inspiration
- see also [Rapid Application Prototyping in PHP Using a Micro Framework](http://net.tutsplus.com/tutorials/php/rapid-application-prototyping-in-php-using-a-micro-framework/).
