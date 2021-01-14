CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Troubleshooting
 * FAQ
 * Maintainers


INTRODUCTION
------------

Drupal core and contrib modules do not currently offer a simple
solution for managing a content type's title in the "Manage Display" tab.

Title Field for Manage Display creates a title field in all content types that
shows up in the "Manage Display" tab. It has a field formatter option named
"Title" that offers to display the node title with the core title formatter
options. This field is added to all content types when the module is enabled.


REQUIREMENTS
------------

This module does not require any modules.


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules
   for further information.


CONFIGURATION
-------------

    1. Visit any content type's manage display tab
       (e.g. /admin/structure/types/manage/article/display).
    2. Place the "Title" field in the location you want your title to show up.
    3. Select the "Title" field formatter.
    4. Test the content type's display to view the Title.


This title field will only render if the "Title" field formatter is selected.
Otherwise the node needs to be saved before so that field_display_title can be
updated with the node title.
