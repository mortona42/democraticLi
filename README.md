# Introduction

DemocraticLi is a place to learn about political issues and candidates, and discuss the policies and solutions we would like to put in place.

This project began at a DemocracyLab hackathon in January 2021. We're a group of designers and developers looking to grow our skills and make a difference.

We currently meet on Wednesdays at 5pm PST, see the meetup link below for details.

The name comes from the Confucious concept of Li, roughly translated as "The way".

Applied to democracy, we are working together to define the way to govern ourselves.



* [**DemocraticLi.com**](https://democraticli.com/)
* [**Figma**](https://www.figma.com/file/71XyGfXXELYi15oHK9O0mn/Democratli?node-id=0%3A1)
* [**Trello**](https://trello.com/b/IdnsxNQW/frontend)
* [**Github**](https://github.com/mortona42/democraticLi)
* [**Meetup**](https://www.meetup.com/social-tech-projects)
* [**DemocracyLab**](https://democracylab.org/index/?section=AboutProject&id=640)
* [**Style Guide**](https://mortona42.github.io/democraticLi/)

# Technology
This repository contains the Drupal back end for the site. We can quickly create the architecture for the content we want to store and display, with comments on every item.

You can use Lando to host the site locally. This is a Docker based tool that will run a php server and mysql container for Drupal to run in.

The front end is implemented as an independent style guide, split into styles and components. This lets developers contribute little pieces at a time and keep the codebase clean. As the features are implemented in the back end, we'll implement the style guide as a pallette, applying the css classes to the html produced by drupal, and customizing templates as needed.

Drupal has a robust API out of the box. You can query for content and related content, limit the fields you're fetching, and sort the results. This makes it easy to use frameworks like react or vue as components on the site or standalone applications.

# Project Installation
1. Install Docker and [Lando](https://docs.lando.dev/basics/installation.html).
2. Clone the project.
3. Run `lando start` in the project directory.
4. Open https://democraticli.lndo.site.
5. Install Drupal.

At this point, you should have a copy of the site structure, but no content or files. Reach out to a project member on Discord for further help.

# Front End Development
The theme code is in /web/themes/custom/democraticli.

## CSS
1. Edit sass files in /web/themes/custom/democraticli/scss.
2. Run npm install to get sass modules.
3. Run npm run-script sass-compile to compile sass to css.
4. Run npm run-script sass-watch to watch for more changes.

## HTML
1. Inspect the element you want to change and find the template it's using.
2. Copy the template to /web/themes/custom/democraticli/templates.
3. Make your changes to the template file.
4. Clear the cache to load the new template and see your changes.

