{**
 @todo add class description, id, modification date, obj count?
 @todo limit attr. description col width
*}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|i18n('SysInfo')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline">{$description|wash}</div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}{*<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">*}
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{* Items per page and view mode selector. *}

{def $policies = ''
     $i = 0
     $limit_location_array = array()
     $limit_node = false()
     $limit_section = ''}
{*foreach $classes as $class}
    {if gt($class.modified, $modified)}
        {set $modified = $class.modified}
    {/if}
{/foreach}
{'Classes last modified'|i18n('SysInfo')}: {$modified|l10n( shortdatetime )*}

{foreach $roles as $role}
    <div style="clear:both">
    <h2>{$role.name|wash()}</h2>

    <pre>
    <table class="list">
    <tr>
        <th>{'Policies'|i18n('SysInfo')}</th>
        <th>{'Module'|i18n('SysInfo')}</th>
        <th>{'Function'|i18n('SysInfo')}</th>
        <th>{'Limitations'|i18n('SysInfo')}</th>
    </tr>
    {set $i = 1}
    {foreach $role.policies as $policy sequence array( 'bglight', 'bgdark') as $style}
    <tr class="{$style}">
        <td>{$i}</td>
        <td>{$policy.module_name|wash}</td>
        <td>{$policy.function_name|wash}</td>
        <td>{foreach $policy.limitations as $limitation}{$limitation.identifier|wash}({foreach $limitation.values_as_array_with_names as $limitation_value}{$limitation_value|wash}{delimiter}, {/delimiter}{/foreach}){delimiter}, {/delimiter}{/foreach}</td>
        {set $i = $i|inc()}
    </tr>
    {/foreach}
    </table>

    <table class="list">
    <tr>
        <th>{'Assignments'|i18n('SysInfo')}</th>
        <th>{'User/group'|i18n('SysInfo')}</th>
        <th>{'Limitation'|i18n('SysInfo')}</th>
    </tr>
    {set $i = 1}
    {foreach $role.user_array as $name => $user sequence array( 'bglight', 'bgdark') as $style}
    <tr class="{$style}">
        <td>{$i}</td>
        <td>{$name|wash}</td>
        <td>
        {if $user.limit_ident}
            {if $user.limit_value|begins_with( '/' )}
                 {set $limit_location_array=$user.limit_value|explode( '/' )
                      $limit_node=fetch('content','node', hash('node_id', $limit_location_array[$limit_location_array|count()|sub(2)] ))}
                 {$user.limit_ident|wash}:&nbsp;"{$limit_node.name|wash}"&nbsp;({$user.limit_value|wash})
             {else}
                 {set $limit_section=fetch( 'section', 'object', hash( 'section_id', $user.limit_value ) )}
                 {$user.limit_ident|wash}:&nbsp;"{$limit_section.name|wash}"&nbsp;({$user.limit_value|wash})

              {/if}
        {/if}
        </td>
        {set $i = $i|inc()}
    </tr>
    {/foreach}
    </table>
    </pre>

{/foreach}
{undef $policies $i $limit_location_array $limit_node $limit_section}

{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>
