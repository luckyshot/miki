miki
================

Miki is a mini-wiki system in just one file.

 - One single PHP file
 - No database needed (files are stored as .txt files in the server)
 - Auto-installed automatically
 - Full Markdown language support
 - Super fast and lightweight (0.01MB including PHP code, Database, Responsive Stylesheet and Scripts)

![Miki mini-wiki system screenshot](http://xaviesteve.com/wp-content/uploads/2013/02/Screen-Shot-2013-02-02-at-10.45.37.png)

How to use it
----------------------

At the right side every page you will find an _Edit_ button, click on it and modify the text, then click _Save_ (or press `Control+S`).

Use [Markdown](http://daringfireball.net/projects/markdown/syntax) to format your text and put words inside `[  ]` to create internal links, then click on them and a new page will be created.


Installation
----------------------

1. Copy `index.php` to your web server
2. Open `index.php` and customize details in `$config` variable
3. Create a folder next to `index.php` with a very very long name (30 characters or more)
4. Create a link in your browser to point to _URL + FOLDERNAME_. Example: `http://example.com/miki/myfoldername`
5. Make sure that you have writing permissions in the Miki folder, every new Miki page is stored as a `.txt` file

Custom CSS styling
----------------------

Create a link `[customcss]` so you get a page like http://example.com/miki/customcss/ and write the CSS code in it.

Changelog
---------------------

### 3.1.24 (10 April 2017)

- Auto-save drafts
- Dark mode at night

### 3.1.20 (13 February 2017)

- Simplified setup + auto-installation of `.htaccess`
- Multi-user support (one per folder)
- User Interface improvements and code optimizations
- Cookie based login allows for long session durations
- Keyboard shortcut to _Edit_ and _Save_
- Dark theme between 9pm and 7am
- Custom starting page name (`welcome` by default)
- Custom per-folder CSS file
- New logo and favicon
- Tons of bug fixes and performance improvements


### 2.0.2 (2 February 2013)

- Ported all formatting to official Markdown
- New design (better readability, responsive, mobile optimized)
- Huge bandwidth savings: Formatted text is now generated on the fly (instead of downloading formatted+unformatted)
- Lots of bug fixes and performance improvements



License
---------------------

Miki is authored by [Xavier Esteve](https://xaviesteve.com/) and licensed under a [Creative Commons Attribution-NonCommercial-ShareAlike license](http://creativecommons.org/licenses/by-nc-sa/3.0/).


Disclaimer
---------------------

Please check the code by yourself before using it in a production environment.
