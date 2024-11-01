=== Ultimate-Subversion ===
Contributors: zero-one
Donate link: http://zero-one.ch/
Tags: subversion, coding, integration, svn
Requires at least: 2.0.2
Tested up to: 3.0.1
Stable tag: 1.0.3

This Plugin shows log entries of a remote Subversion repository. Nice for Developer blogs.
== Description ==

This Plugin allows you to show Subversion logs on Wordpress Pages. You just have to define the remote repositories in the backend, add a short string to the page and done.
All settings are configurable trought the admin interface. The give the plugin a personal touch you can eddit the `styles.css` in the plugin folder.

= Features =
* As much remote (http/https) repositories as you want
* Fully customizeable look of the frontend
* perfect for Development and project Blogs
* Integration of WebSVN
* If the SVN log author is an Blog User. Detail information about the user are used, like email and webpage

== Installation ==

1. Upload the whole Plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure your Repositories over the 'UltimateSubversion' menubutton.
4. Create or edit a Page and put for example %svnlog:REPOSITORYNAME% anywhere in the page content. Where REPOSITORYNAME will be the Value of the Name field from your previously configured repositories in the admin menu.

At the moment there are 2 functions implemented:
* *svnlog*: shows all logs of the repository or the given path in the repository
* *svnhead*: shows only information about the head revision of the repository or the given path in the repository

= Configuration =
In the admin Panel of the plugin you can add your repositories. The following settings are used.
1. *Name* (required): The Name of the repository. Needed to reference to it
1. *Repository* (required): The base path of the repository! IMPORTANT: Do not point to a path in the repository!
1. *Path* (optional): A path in the repository. For example `/trunk/`
1. *Username* (optional): if needed the HTTP Auth Username
1. *Password* (optional): and its password
1. *AlternativeName* (optional): An alternative Name to display on the Page. If not provided, the svn hostname is used.
1. *WebSVNPrefix* (optional): The URL to Websvn. Please provide the whole url. For example: `https://websvn.zero-one.ch/revision.php?repname=REPOSITORYNAME`

Thats all.

== Frequently Asked Questions ==

= Are there any Problems known? =

Yes, currently are the following Problems known:
- You can only display one Repository per page
- There are, in some circumstances, troubles to display a repository over https

= Does it work with Google Code Repositories? =
Yes it does!

== Screenshots ==
1. **HEAD Revsion** - This is how the plugin looks embedded on visiting the page
2. **More Details** - If you click on "show more logs…" 
3. **Page Edit** - How you integrate your subversion to your own pages
4. **Admin** - And this is how the admin Interface looks like

== Changelog ==

= 1.0.3 =
* bug fixing

= 1.0.2 =
* Added Plugin Statistics

= 1.0.1 =
* bug fixing

= 1.0 =
* First stable release
