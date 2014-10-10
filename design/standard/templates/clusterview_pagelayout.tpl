{*
 * A generic pagelayout with nothing else but content, useful for iframe inclusion.
 * We use a somewhat funky name not to clash with other templates used by site developers :-)
 *}
<html>
<head>
    {include uri="design:page_head_style.tpl"}
</head>
<body>
{$module_result.content}
</body>
</html>