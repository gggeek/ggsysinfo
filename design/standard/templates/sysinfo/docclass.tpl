<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}{*<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">*}
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{* Items per page and view mode selector. *}

<table cellpadding="0" border="1" width="100%">
{def $classes=fetch( 'class', 'list',
                           hash( 'sort_by', array( 'name', true() ) ) )}



{def $attributes=''}
{foreach $classes as $class}
<div style="clear:both">
<h2>{$class.name|wash}</h2>
<h3>{'Structure'|i18n('SysInfo')}</h3>
        {set $attributes=fetch( 'class', 'attribute_list', hash( 'class_id', $class.id ) )}
        <table cellpadding="0" border="1" width="100%">
        <tr>
		<th>{'Name'|i18n('SysInfo')}</th>
        <th>{'Identifier'|i18n('SysInfo')}</th>
        <th>{'Datatype'|i18n('SysInfo')}</th>
        <th>{'Required'|i18n('SysInfo')}</th>
        <th>{'Translatable'|i18n('SysInfo')}</th>
        <th>{'Data Collector'|i18n('SysInfo')}</th>
        <th>{'Searchable'|i18n('SysInfo')}</th>
		<th>{'Description'|i18n('SysInfo')}</th>
        </tr>
        {foreach $attributes as $attribute}
            <tr>
            <td>{$attribute.name|wash}</td>
            <td>{$attribute.identifier|wash}</td>
            <td>{$attribute.data_type_string|wash}</td>
				<td>
					{if $attribute.is_required}
						{'Yes'|i18n('SysInfo')}
            {else}
						{'No'|i18n('SysInfo')}
					{/if}
				</td>
            <td>{$attribute.can_translate|wash}</td>
            <td>
            {if $attribute.is_information_collector}
						  {'Yes'|i18n('SysInfo')}
            {else}
						{'No'|i18n('SysInfo')}
            {/if}
            </td>
				<td>
					{if $attribute.is_searchable}
						{'Yes'|i18n('SysInfo')}
            {else}
						{'No'|i18n('SysInfo')}
            {/if}
            </td>
				<td>&nbsp;</td>
            </tr>
        {/foreach}
        </table>
{/foreach}
{undef $attributes}
{undef $classes}



{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>
