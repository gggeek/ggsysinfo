{**
 @todo add class description, id, modification date, obj count?
 @todo limit attr. description col width
*}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|i18n('SysInfo')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}{*<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">*}
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{* Items per page and view mode selector. *}

{*'Classes last modified'|i18n('SysInfo')}: {$modified|l10n( shortdatetime )*}

{foreach $ini_files as $ini => $groups}
    <div style="clear:both">
    <h2>{$ini|wash()}</h2>

    <table class="list">
    <tr>
        <th>{'Block'|i18n('SysInfo')}</th>
        <th>{'Setting'|i18n('SysInfo')}</th>
        <th>{'Value'|i18n('SysInfo')}</th>
    </tr>
    {* @todo fix alternating colors *}
    {foreach $groups as $group => $settings sequence array( 'bglight', 'bgdark' ) as $style}
        {foreach $settings as $setting => $val}
            <tr class="{$style}">
                <td>{$group|wash}</td>
                <td>{$setting|wash()}</td>
                <td>
                {if $val|is_array()}
                    {foreach $val as $key => $value}
                        [{$key|wash}] {$value|wash}{delimiter}<br/>{/delimiter}
                    {/foreach}
                {else}
                    {$val|wash}
                {/if}
                </td>
            </tr>
        {/foreach}
    {/foreach}
    </table>
{/foreach}
{*undef $attributes $classes $LanguageCode*}

{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>
