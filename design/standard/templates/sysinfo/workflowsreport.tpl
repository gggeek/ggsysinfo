<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$title|i18n('SysInfo')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline">{$description|wash}</div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}{*<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">*}
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{foreach $workflows as $workflow }
    <div style="clear:both">
        <h2>{$workflow.name|wash()}</h2>
        <table class="list">
            <tr class="bglight"><td>{'Trigger'|i18n('SysInfo')}</td>
                <td>{foreach $triggers as $trigger}{if eq($trigger.workflow_id, $workflow.id)}{if eq($trigger.connect_type, 'a')}after{else}before{/if} {$trigger.module_name|wash}/{$trigger.function_name|wash}{break}{/if}{/foreach}</td></tr>
            {*<tr class="bgdark"><td>{'Always available'|i18n('SysInfo')}</td><td>{if $class.always_available}{'Yes'|i18n('SysInfo')}{else}{'No'|i18n('SysInfo')}{/if}</td></tr>*}
        </table>
        <table class="list">
            <tr>
                <th>{'Position'|i18n('SysInfo')}</th>
                <th>{'Type'|i18n('SysInfo')}</th>
                <th>{'Settings'|i18n('SysInfo')}</th>
            </tr>
        {foreach $workflow.ordered_event_list as $event sequence array( 'bglight', 'bgdark') as $style}
            <tr class="{$style}">
                <td>{$event.placement}</td>
                <td>{if $event.workflow_type|is_null|not}{$event.workflow_type.group_name|wash}/{$event.workflow_type.name|wash}{/if}</td>
                <td>{if $event.workflow_type|is_null}
                        ERROR
                    {else}
                        {event_view_gui event=$event}
                    {/if}</td>
            </tr>
        {/foreach}
        </table>
    </div>
{/foreach}

{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>
