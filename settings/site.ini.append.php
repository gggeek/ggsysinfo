<?php /*

[TemplateSettings]
ExtensionAutoloadPath[]=ggsysinfo

[RegionalSettings]
TranslationExtensions[]=ggsysinfo

[RoleSettings]
# There is no need to comment this out to insure proper security.
# We use a slightly unusual permission model:
# - all views check for access permissions by themselves
# - we thus need to tell the kernel not to block access to the views
# - using PolicyOmitList[]=sysinfo/oneview, it is possible to allow anon access to one specific view
PolicyOmitList[]=sysinfo

# The following view is even more strange: it checks permissions by using a token in the db.
# It is used for server-2-server communication in eZ clusters, and has to be accessible in backoffice siteaccesses
PolicyOmitList[]=sysinfo/clusterslave

[SiteAccessSettings]
AnonymousAccessList[]=sysinfo/clusterslave

# Cache item entry (for eZ Publish 4.3 and up)
[Cache]
CacheItems[]=sysinfo

[Cache_sysinfo]
name=ggSysInfo extension graph cache
path=sysinfo
