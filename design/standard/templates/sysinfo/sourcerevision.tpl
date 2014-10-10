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

<pre>

<table class="list" cellspacing="0">
  <tr>
    <th>GIT Revision</th>
  </tr>
{foreach $revision_info as $result sequence array( 'bglight', 'bgdark') as $style}
  <tr class="{$style}">
    <td>{$result|wash()}</td>
  </tr>
{/foreach}
</table>

<table class="list" cellspacing="0">
  <tr>
    <th>GIT Status</th>
  </tr>
{foreach $status_info as $result sequence array( 'bglight', 'bgdark') as $style}
  <tr class="{$style}">
    <td>{$result|wash()}</td>
  </tr>
{/foreach}
</table>

</pre>

</div>


<p>Full history of source code is available at: <a href="https://bitbucket.org/itmebusiness/ovumkc">ITMEBusiness Bitbucket</a></p>

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>