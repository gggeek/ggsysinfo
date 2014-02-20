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

{if $rows}
<table class="list" cellspacing="0">
  <tr>
    <th>Line</th>
    <th>File</th>
    <th>Content</th>
    <th>Warning</th>
  </tr>
{foreach $rows as $row sequence array( 'bglight', 'bgdark') as $style}
    <tr class="{$style}">
    <td>{$row.2|wash()}</td>
    <td>{if $ezgeshi_available}<a href={concat('/geshi/highlight/',$row.1,'/(language)/',$fileformat)|ezurl()}>{/if}{$row.1|wash()}{if $ezgeshi_available}</a>{/if}</td>
    <td style="font-family: monospace;">{$row.3|wash()}</td>
    <td>{$row.0|wash()}</td>
  </tr>
{/foreach}
</table>
{else}
  No problems detected
{/if}

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>
