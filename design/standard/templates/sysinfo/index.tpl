<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|wash()}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<p>An interface aiming to complement the "Setup" tab from the standard administration interface, of use mostly to system administrator and developers.</p>

<table class="list" cellspacing="0">
{def $prefix=''}
{foreach $groups as $name => $exts offset 1}
      {if eq($name, 'Reports')}
          {set $prefix = 'layout/set/print/'}
      {else}
          {set $prefix = ''}
      {/if}
      <tr>
          <th>{$name}</th>
          <th>Description</th>
      </tr>
    {foreach $exts as $i => $ext sequence array( 'bglight', 'bgdark') as $style}
      <tr class="{$style}">
          <td>{if $ext.disabled}{$ext.name}{else}<a href={concat($prefix, 'sysinfo/', $i)|ezurl()}>{$ext.name}</a>{/if}</td>
          <td>{$ext.description}</td>
      </tr>
    {/foreach}
{/foreach}
</table>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>