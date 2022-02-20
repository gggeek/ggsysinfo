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

{def $classes = fetch( 'class', 'list', hash( 'sort_by', array( 'name', true() ) ) )
     $attributes = ''
     $LanguageCode = ''
     $modified = 0}
{foreach $classes as $class}
    {if gt($class.modified, $modified)}
        {set $modified = $class.modified}
    {/if}
{/foreach}
{'Classes last modified'|i18n('SysInfo')}: {$modified|l10n( shortdatetime )}

{foreach $classes as $class}
    <div style="clear:both">
    <h2>{$class.name|wash()} [{$class.identifier}]</h2>
    {$class.descriptionList[$language_code]|wash}
    <pre>
    <table class="list">
    <tr class="bglight"><td>{'Container'|i18n('SysInfo')}</td><td>{if $class.is_container}{'Yes'|i18n('SysInfo')}{else}{'No'|i18n('SysInfo')}{/if}</td></tr>
    <tr class="bgdark"><td>{'Always available'|i18n('SysInfo')}</td><td>{if $class.always_available}{'Yes'|i18n('SysInfo')}{else}{'No'|i18n('SysInfo')}{/if}</td></tr>
    <tr class="bglight"><td>{'Object name pattern'|i18n('SysInfo')}</td><td>{$class.contentobject_name|wash()}</td></tr>
    <tr class="bgdark"><td>{'URL alias pattern'|i18n('SysInfo')}</td><td>{$class.url_alias_name|wash()}</td></tr>
    </table>
    {* @todo find a smarter way to get the language for the attricbute description*}
    {set $attributes = fetch( 'class', 'attribute_list', hash( 'class_id', $class.id ) )
         $LanguageCode = $class.top_priority_language_locale}
    <table class="list">
    <tr>
        <th></th>
        <th>{'Attribute'|i18n('SysInfo')}</th>
        <th>{'Identifier'|i18n('SysInfo')}</th>
        <th>{'Datatype'|i18n('SysInfo')}</th>
        <th>{'Required'|i18n('SysInfo')}</th>
        <th>{'Searchable'|i18n('SysInfo')}</th>
        <th>{'Info Collector'|i18n('SysInfo')}</th>
        <th>{'Translatable'|i18n('SysInfo')}</th>
        <th>{'Description'|i18n('SysInfo')}</th>
    </tr>
    {foreach $attributes as $i => $attribute sequence array( 'bglight', 'bgdark') as $style}
    <tr class="{$style}">
        <td>{$i|inc()}</td>
        <td>{$attribute.name|wash}</td>
        <td>{$attribute.identifier|wash}</td>
        <td>{$attribute.data_type_string|wash}</td>
        <td>{if $attribute.is_required}{'Yes'|i18n('SysInfo')}{else}{'No'|i18n('SysInfo')}{/if}</td>
        <td>{if $attribute.is_searchable}{'Yes'|i18n('SysInfo')}{else}{'No'|i18n('SysInfo')}{/if}</td>
        <td>{if $attribute.is_information_collector}{'Yes'|i18n('SysInfo')}{else}{'No'|i18n('SysInfo')}{/if}</td>
        <td>{if $attribute.can_translate}{'Yes'|i18n('SysInfo')}{else}{'No'|i18n('SysInfo')}{/if}</td>
        <td>{if is_set($attribute.descriptionList)}{$attribute.descriptionList[$language_code]|wash()}{else}&nbsp;{/if}</td>
    </tr>
    {/foreach}
    </table>
    </pre>
{/foreach}

{undef $attributes $classes $LanguageCode}

{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>

<hr>
{def $timestamp=currentdate()}
Host: {$hostname|wash}; date: {$timestamp|l10n( 'shortdatetime' )}
</div>
