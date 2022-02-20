<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|wash()}</h1>

{* DESIGN: Mainline *}<div class="header-mainline">{$description|wash}</div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<div class="context-attributes">

{if ne($css, "")}
<link rel="stylesheet" type="text/css" href={concat('stylesheets/',$css)|ezdesign()} />
{/if}

{if $warnings}
<table class="list" cellspacing="0">
  <tr>
    <th>Problems detected</th>
  </tr>
{foreach $warnings as $warning sequence array( 'bglight', 'bgdark') as $style}
  <tr class="{$style}">
    <td>{$warning|wash()}</td>
  </tr>
{/foreach}
</table>
{else}
  No problems detected
{/if}

</div>

See also the <a href={'setup/systemupgrade'|ezurl}>Database Consistency Check</a> page for more tests

{* DESIGN: Content END *}</div></div></div></div></div></div>

<hr>
{def $timestamp=currentdate()}
Host: {$hostname|wash}; date: {$timestamp|l10n( 'shortdatetime' )}
</div>
