# Custom Markup Block


## Contents of this file

* Introduction
* Requirements
* Recommended modules
* Installation
* Configuration
* Maintainers


## Introduction

The Custom Markup Block module provides a block that renders configured markup
when creating the block instance. The benefit of this module is that the markup
gets added to the site configuration rather than entity content (block types),
which can be made available to all web application environments by configuration
export/import.

For example, this module would be useful if you want a block of static text
which is not intended to be edited by content editors and you want the text in
config so it can be version controlled and deployed.


## Requirements

This module requires no modules outside of Drupal core.


## Recommended modules

 * Token Filter (https://www.drupal.org/project/token_filter)
   Makes token values available as an input filter, providing such feature to
   the configured content in your custom markup bocks.


## Installation

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.


## Configuration

The module has no menu or modifiable settings. There is no configuration. When
enabled, the module will allow the creation of a new Custom Markup Block from
Administration > Structure > Block layout.


## Maintainers

* Jeremy Graham (jez500) - https://www.drupal.org/user/459856
* Manuel Adan (manuel.adan) - https://www.drupal.org/user/516420
