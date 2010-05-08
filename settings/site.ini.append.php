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

*/ ?>