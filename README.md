miki
================

Miki is a mini-wiki system in just one file.

 - One single PHP file (and an htaccess)
 - No database needed (files are stored as .txt files in the server)
 - Markdown language

![Miki mini-wiki system screenshot](http://xaviesteve.com/wp-content/uploads/2013/02/Screen-Shot-2013-02-02-at-10.45.37.png)

How to use it
----------------------

At the right of every page you will find an *Edit* button, click on it and modify the text, then Save.

Use [Markdown](http://daringfireball.net/projects/markdown/syntax) to format your text and put words inside `[  ]` to create internal links, then click on them and a new page will be created.


Installation
----------------------

1. Copy `.htaccess` and `index.php` into your web server.
2. Open `index.php` and change `$apppass` to something long.
3. Also, set the `$url` and `$apppath`.
4. Open `.htaccess` and set the RewriteBase path.
5. Create a link in your browser to point to `$url/$apppass`. Example: *http://example.com/miki/mysuperlongapppass*
6. Make sure that you have writing permissions in the Miki folder, every new Miki page is stored in a `.txt` file.


Changelog
---------------------

### 2.0.2 (2 February 2013)
- Ported all formatting to official Markdown
- New design (better readability, responsive, mobile optimised)
- Huge bandwidth savings: Formatted text is now generated on the fly (instead of downloading formatted+unformatted)
- Lots of bug fixes and performance improvements



License
---------------------

Miki is authored by [Xavi Esteve](http://xaviesteve.com/) and licensed under a [Creative Commons Attribution-NonCommercial-ShareAlike license](http://creativecommons.org/licenses/by-nc-sa/3.0/).


Disclaimer
---------------------

There are still some things to iron out. The app right now is a great concept though. This code hasn't been tested in a production environment or been under any security audit. While it should be safe, please check the code by yourself, I am not responsible for any damage.
