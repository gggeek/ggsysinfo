{*
 * A 'meta' view, holding the results for all clustered nodes data
 *}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|wash()}</h1>

{* DESIGN: Mainline *}<div class="header-mainline">{$description|wash}</div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* @todo simplify a bit the css and make the loading image centered *}
<style type="text/css">
{literal}
    .pageLoader {position:absolute; z-index:999;}
    .pageLoader div {display:table-cell; width:100%; height:300px; background:#fff; text-align:center; vertical-align:middle;}
{/literal}
</style>

{def $i = 0}
{foreach $cluster_nodes as $node_name => $node_url}
    <div>
        <h2>{$node_name|wash}</h2>
        <div id="pageloader_{$i}" class="pageLoader"><div><img src={'pageloader.gif'|ezimage} /></div></div>
        <iframe src="{$node_url}" width="100%" height="300px" marginheight="0" frameborder="0" onload="document.getElementById('pageloader_{$i}').style.display='none';">Iframe support needed to display properly</iframe>
    </div>
    {set $i = inc( $i )}
{/foreach}
{undef $i}

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
