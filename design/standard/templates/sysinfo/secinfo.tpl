<!--<form name="cacheaction" method="post" action="">-->

{ezcss_require('secinfo.css')}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|i18n('SysInfo')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline">{$description|wash}</div>

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

<table class="list secinfo" cellspacing="0">

{foreach $results.test_results as $i => $section}
	<tr>
        <th>{$i|upcase()|wash()}</th>
        <th>{'Test'|i18n( 'SysInfo')}</th>
        <th>{'Details'|i18n( 'SysInfo')}</th>
        <th>{'Value'|i18n( 'SysInfo')}</th>
	</tr>
{foreach $section as $name => $testresult  sequence array( 'bglight', 'bgdark') as $style}
    <tr class="{$style}">
{switch match=$testresult.result}
    {case match='-1'}
        <td class="message">{'OK'|i18n('SysInfo')}</td>
    {/case}
    {case match='-2'}
        <td class="notice">{'NOTICE'|i18n('SysInfo')}</td>
    {/case}
    {case match='-4'}
        <td class="warning">{'KO'|i18n('SysInfo')}</td>
    {/case}
    {case match='-1024'}
        <td class="error">{'ERROR'|i18n('SysInfo')}</td>
    {/case}
    {case}{* includes 2048=not_run*}
         <td class="error">{'NA'|i18n('SysInfo')}</td>
    {/case}
{/switch}

        <td>{$name|wash()}</td>
        <td>
            {$testresult.message|strip_tags|wash()}
            {if ne($testresult.moreinfo_url, '')}<br/>{'More info'|i18n('Sysinfo')}: <a href="{$testresult.moreinfo_url|wash()}">{$testresult.moreinfo_url|wash()}</a>{/if}
        </td>
        <td>
            {'Current'|i18n('Sysinfo')}: {$testresult.value_current|wash()}<br/>
            {'Recommended'|i18n('Sysinfo')}: {$testresult.value_recommended|wash()}
        </td>
    </tr>
{/foreach}

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

<hr>
{def $timestamp=currentdate()}
Host: {$hostname|wash}; date: {$timestamp|l10n( 'shortdatetime' )}
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
