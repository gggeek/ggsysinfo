<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|wash()}</h1>

<p>Mysql client per-process statistics. For details about their meaning see the <a href="http://www.php.net/manual/en/mysqlnd.stats.php">PHP manual page</a></p>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<div class="context-attributes">

{if ne($css, "")}
<link rel="stylesheet" type="text/css" href={concat('stylesheets/',$css)|ezdesign()} />
{/if}

{if $stats}
<table class="list" cellspacing="0">
  <tr>
    <th>Stat</th>
    <th>Value</th>
  </tr>
{foreach $stats as $key => $val sequence array( 'bglight', 'bgdark') as $style}
    <tr class="{$style}">
    <td>{if $important_stats|contains( $key )}<b>{/if}{$key|wash()}{if $important_stats|contains( $key )}</b>{/if}</td>
    <td>{if $important_stats|contains( $key )}<b>{/if}{$val|wash()}{if $important_stats|contains( $key )}</b>{/if}</td>
  </tr>
{/foreach}
</table>
{else}
  No stats available
{/if}

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

<hr>
{def $timestamp=currentdate()}
Host: {$hostname|wash}; date: {$timestamp|l10n( 'shortdatetime' )}
</div>
