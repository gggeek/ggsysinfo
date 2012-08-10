<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|wash()}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

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
{foreach $warnings as $warning => $details sequence array( 'bglight', 'bgdark') as $style}
    <td>{include uri=concat('design:setup/tests/', $warning, '_error.tpl') test_result=$details result_number=''}</td>
  </tr>
{/foreach}
</table>
{else}
  No problems detected
{/if}

</div>

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>