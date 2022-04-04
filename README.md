Extension ggsysinfo for eZ publish
===================================

Goals
-----

Allow eZ Publish administrators and developers to have better insight into their working installation.

For example:
- have the complete information from the installed php accelerator within the eZ Publish admin interface, and, in case, allow to clear its cache
- have a more detailed view on eZ Publish cache files, allowing to grep through them and delete them if needed
- have a more detailed view on eZ Publish log files, allowing to grep through them
- have a stats page with all types of content in the db listed
- have a page with more detailed status tests than ezinfo/isalive
- have the complete information from the phpinfo() page within the eZ Publish admin interface
- and more... new information panels are added periodically to the extension

Some pages that can be of help to developers too:
- all available modules
- all available module views with their parameters
- all available fetch functions
- all available policy functions
- all available policy module operations
- all available template operators (eZ4 templates)
- all available workflow event types

Requirements
------------
- php 5.3 or later
- eZ Publish 4.0 or later (tested all the way to eZ Platform 2.5)
- eZ Publish cluster mode is only partially supported for the cache info part
- the GD extension to php plus the Zeta Components library are needed to produce graphs (eg. in the storage churn page)
- the ezgeshi extension ver. 1.3 or later is an optional add-on that will add some functionality

Installation
------------
- As per standard eZ Publish extensions: drop in extension dir, activate in site.ini override file or via gui
- fix the autoload config: if not activated via the admin interface, run the ezpgenerateautoloads.php script
- to have the connecting-to-the-web and connecting-to-email-server tests activated, go edit sysinfo.ini.append.php.
  By default, these tests are deactivated since they involve active probing of external resources
- Access to all views of this module is regulated by granting access to the "setup/system_info" policy.
  NB: this is the same policy that governs access to the "System information" page in the admin interface,
  not a new policy defined by this extension.
  To allow anonymous access to a single view, the ini setting PolicyOmitList can be also used.
- Make sure that users can access image files stored in var/$sitename/cache/sysinfo for the storage churn graph to work
  (this means properly configuring .htaccess file or vhost configuration usually)

- For setting up the extension properly in cluster mode, read the details in settings/sysinfo.ini, [ClusterSettings] section

- If the ezgeshi extension ver. 1.3 or later is also installed, on the pages listing views and fetch functions
  a link to the source code will be available. To be able to see the source listing, permission to the policy
  geshi/view_ezpublish_source must be granted for the desired users.

New modules/views
-----------------
All the functionality is available in a new top-level tab in the administration interface, named "System Information".
A somewhat-detailed description of all available information pages is given there.
If some of the items in the menu are greyed out, it is because the corresponding item does not work / apply to your eZ Publish setup.

The systemstatus view can be used for systematic monitoring of the status of the eZ Publish installation by external tools such as Nagios, OpenNMS, Zenoss etc...
For such configurations, it is recommended to use the json output, activated by appending 'json' to the url (after of course, giving correct access permissions):
http://my.ezpublish.site/index.php/<siteaccess>/sysinfo/systemstatus/(view)/json
