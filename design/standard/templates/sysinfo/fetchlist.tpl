<!--<form name="cacheaction" method="post" action="">-->

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|i18n('SysInfo')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}{*<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">*}
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{* Items per page and view mode selector. *}
{*
<div class="context-toolbar">
<div class="block">
<div class="left">
    <p>
    {switch match=$number_of_items}
        {case match=25}
        <a href={'/user/preferences/set/admin_list_limit/1/content/draft'|ezurl}>10</a>
        <span class="current">25</span>
        <a href={'/user/preferences/set/admin_list_limit/3/content/draft'|ezurl}>50</a>
        {/case}

        {case match=50}
        <a href={'/user/preferences/set/admin_list_limit/1/content/draft'|ezurl}>10</a>
        <a href={'/user/preferences/set/admin_list_limit/2/content/draft'|ezurl}>25</a>
        <span class="current">50</span>
        {/case}

        {case}
        <span class="current">10</span>
        <a href={'/user/preferences/set/admin_list_limit/2/content/draft'|ezurl}>25</a>
        <a href={'/user/preferences/set/admin_list_limit/3/content/draft'|ezurl}>50</a>
        {/case}
    {/switch}
    </p>
</div>
<div class="break"></div>
</div>
</div>
*}

<table class="list" cellspacing="0">
    <tr>
        <th>{'Name'|i18n( 'SysInfo')}</th>
        <th>{'Module'|i18n( 'SysInfo')}</th>
        <th>{'Extension'|i18n( 'SysInfo')}</th>
        <th>{'Parameters'|i18n( 'SysInfo')}</th>
        <th>{'Call method'|i18n( 'SysInfo')}</th>
        <th>{'Source'|i18n( 'SysInfo')}</th>
        <th>{'Help'|i18n( 'SysInfo')}</th>
    </tr>
{def $callmethod = array()
     $native     = false()
     $basedocurl = concat(ezini('GeneralSettings', 'DocRoot', 'sysinfo.ini').fetches,'/')
     $docsuffix  = ezini('GeneralSettings', 'PageSuffix', 'sysinfo.ini')
     $basedoxurl = concat(ezini('GeneralSettings', 'DocRoot', 'sysinfo.ini').sourcecode,$sdkversion,'/')}
{foreach $fetchlist as $fetch => $details sequence array( 'bglight', 'bgdark') as $style}
    {set $native = eq($details['extension'], '')}
    <tr class="{$style}">
        <td>
            {$details['name']|wash}
        </td>
        <td>
            {$details['module']|wash}
        </td>
        <td>
            {$details['extension']|wash}
        </td>
        <td>
            {foreach $details['parameters'] as $id => $param}{$param['name']|wash} ({$param['type']|wash}){if $param['required']} required{/if}{delimiter}<br/>{/delimiter}{/foreach}
        </td>
        {set $callmethod = $details['call_method']}
        <td>
            {if and(is_set($callmethod['class']), is_set($callmethod['method']))}
                {$callmethod['class']}::{$callmethod['method']}
            {/if}
        </td>
        <td>
            {if and(is_set($callmethod['class']), is_set($callmethod['method']))}
                {if $ezgeshi_available}
                    <a href={concat('/geshi/highlight/',$callmethod['include_file'])|ezurl}>local</a>
                {else}
                    {if and($native, $source_available)}<a href="{concat($basedoxurl,$callmethod['include_file']|urlencode())}">github</a>{/if}
                {/if}
            {/if}
        </td>
        <td>
            {if $native}<a href="{concat($basedocurl,$details['module'],'/Fetch-functions/',$details['name'],$docsuffix)}">ez.no{/if}
        </td>
    </tr>
{/foreach}
</table>

{*
<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/sysinfo/ezcache'
         item_count=$list_count
         view_parameters=$view_parameters
         item_limit=$number_of_items}
</div>
*}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
{*<div class="block">
{if gt($list_count, 0)}
    <input class="button" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'SysInfo')}" title="{'Remove selected files.'|i18n( 'SysInfo' )}" />
    <input class="button" type="submit" name="EmptyButton"  value="{'Remove all'|i18n( 'SysInfo')}" onclick="return confirmDiscard( '{'Are you sure you want to remove all cache files found?'|i18n( 'design/admin/content/draft' )|wash(javascript)}' );" title="{'Remove all cache files found.'|i18n( 'SysInfo' )}" />
{else}
    <input class="button-disabled" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'SysInfo')}" disabled="disabled" />
    <input class="button-disabled" type="submit" name="EmptyButton"  value="{'Remove all'|i18n( 'SysInfo')}" disabled="disabled" />
{/if}
</div>*}
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>

<!--</form>-->

{*literal}
<script language="JavaScript" type="text/javascript">
<!--
    function confirmDiscard( question )
    {
        // Ask user if he really wants to do it.
        return confirm( question );
    }
-->
</script>
{/literal*}
