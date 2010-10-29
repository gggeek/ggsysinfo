
{def $sysinfogroups = sysinfomoduleviews()
     $prefix = ''}
{foreach $sysinfogroups as $name => $exts offset 1}
    {if eq($name, 'Reports')}
        {set $prefix = 'layout/set/print/'}
    {else}
        {set $prefix = ''}
    {/if}
    {* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
    <h4>{$name}</h4>
    {* DESIGN: Header END *}</div></div></div></div></div></div>
    {* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">
    <ul>
    {foreach $exts as $i => $ext}
        {if $ext.hidden|not()}
        <li><div>{if $ext.disabled}<span class="disabled">{$ext.name}</span>{else}<a href={concat($prefix, 'sysinfo/', $i)|ezurl()}>{$ext.name}</a>{/if}</div></li>
        {/if}
    {/foreach}
    </ul>
    {* DESIGN: Content END *}</div></div></div></div></div></div>
{/foreach}
{undef $sysinfogroups $prefix}
