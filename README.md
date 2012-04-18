miki
================

Miki is a mini-wiki system that consists of only one core file.

 - One single PHP file (and an htaccess)
 - No database needed
 - Basic formatting language optimised for mobile and iPad (like Markdown but simpler)

How to use it
----------------------

At the bottom left of every page you will find an *Edit* button, click on it and modify the text.

	. Heading 1 .
	.. Heading 2 ..
	... Heading 3 ...
	- List item
	**bold**
	''italics''
	[internal link]
	http://externallink.com
	<strong>Any HTML</html>

Installation
----------------------

1. Copy `.htaccess` and `index.php` into your web server.

2. Open `index.php` and change `$apppass` to something long.

3. Also, set the `$url` and `$apppath`.

4. Open `.htaccess` and set the RewriteBase path.

5. Create a link in your browser to point to `$url/$apppass`. Example: *http://example.com/miki/mysuperlongapppass*

6. Make sure that you have writing permissions in the Miki folder, every new Miki page is stored in a `.txt` file.

Notes
---------------------

There are still some things to iron out. The app right now is a great concept though. This code hasn't been tested in a production environment or been under any security audit. While it should be safe, please check the code by yourself.
