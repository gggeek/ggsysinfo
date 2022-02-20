<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Log churn'|i18n('SysInfo')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline">{$description|wash}</div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">
{* DESIGN: Content START *}{*<div class="box-ml"><div class="box-mr"><div class="box-content">*}

{if ne($errormsg, '')}
    {$errormsg}
{/if}
{foreach $graphsources as $title => $graphsource}
    <div class="context-attributes">
    <h2>{$title}</h2>
    {if ne($graphsource, false())}
        <a href={concat('sysinfo/logview/',$title)|ezurl()}><img src="{concat(ezroot('no'),$graphsource)}" alt="{$title|i18n('SysInfo')}" /></a>
    {else}
        {'No log file'|i18n('SysInfo')}
    {/if}
    </div>
{/foreach}

{* DESIGN: Content END *}</div></div></div></div></div></div>
{* DESIGN: Content END *}{*</div></div></div>*}

<hr/>
{def $timestamp=currentdate()}
Host: {$hostname|wash}; date: {$timestamp|l10n( 'shortdatetime' )}
</div>
