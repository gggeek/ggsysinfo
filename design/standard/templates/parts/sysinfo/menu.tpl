{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h4>PHP</h4>
{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">
{def $currentcache=installedphpcache()}
<ul>
    <li><div><a href={'/sysinfo/php'|ezurl()}>phpinfo()</a></div></li>
    <li><div>{if ne($currentcache,'apc')}<span class="disabled">APC</span>{else}<a href={'/sysinfo/apc'|ezurl()}>APC</a>{/if}</div></li>
    <li><div>{if ne($currentcache,'eaccelerator')}<span class="disabled">eAccelerator</span>{else}<a href={'/sysinfo/eaccelerator'|ezurl()}>eAccelerator</a>{/if}</div></li>
    <li><div>{if ne($currentcache,'xcache')}<span class="disabled">XCache</span>{else}<a href={'/sysinfo/xcache'|ezurl()}>XCache</a>{/if}</div></li>
	<li><div>{if ne($currentcache,'wincache')}<span class="disabled">WinCache</span>{else}<a href={'/sysinfo/wincache'|ezurl()}>WinCache</a>{/if}</div></li>
</ul>
{* DESIGN: Content END *}</div></div></div></div></div></div>

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h4>eZ Publish</h4>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">
<ul>
    <li><div><a href={'/sysinfo/systemstatus'|ezurl()}>{'System status'|i18n('SysInfo')}</a></div></li>
{def $fileHandler = ezini('ClusteringSettings', 'FileHandler', 'file.ini')
     $acceptedHandlers = array('ezfs', 'eZFSFileHandler', 'eZFS2FileHandler')}
{if $acceptedHandlers|contains($fileHandler)}
    <li><div><a href={'/sysinfo/cachestats'|ezurl()}>{'Cache stats'|i18n('SysInfo')}</a></div></li>
    <li><div><a href={'/sysinfo/cachesearch'|ezurl()}>{'Cache search'|i18n('SysInfo')}</a></div></li>
    <li><div><a href={'/sysinfo/storagestats'|ezurl()}>{'Storage stats'|i18n('SysInfo')}</a></div></li>
{else}
    <li><div><span class="disabled">{'Cache stats'|i18n('SysInfo')}</span></div></li>
    <li><div><span class="disabled">{'Cache search'|i18n('SysInfo')}</span></div></li>
    <li><div><span class="disabled">{'Storage stats'|i18n('SysInfo')}</span></div></li>
{/if}
    <li><div><a href={'/sysinfo/storagechurn'|ezurl()}>{'Storage churn'|i18n('SysInfo')}</a></div></li>
    <li><div><a href={'/sysinfo/contentstats'|ezurl()}>{'Content stats'|i18n('SysInfo')}</a></div></li>
    <li><div><a href={'/sysinfo/logstats'|ezurl()}>{'Log stats'|i18n('SysInfo')}</a></span></div></li>
    <li><div><span class="disabled">{*<a href={'/sysinfo/logsearch'|ezurl()}>*}{'Log search'|i18n('SysInfo')}{*</a>*}</span></div></li>
    <li><div><a href={'/sysinfo/logchurn'|ezurl()}>{'Log churn'|i18n('SysInfo')}</a></span></div></li>
</ul>
{* DESIGN: Content END *}</div></div></div></div></div></div>

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h4>Development</h4>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">
<ul>
    <li><div><a href={'/sysinfo/modulelist'|ezurl()}>{'Modules'|i18n('SysInfo')}</a></div></li>
    <li><div><a href={'/sysinfo/viewlist'|ezurl()}>{'Views'|i18n('SysInfo')}</a></div></li>
    <li><div><a href={'/sysinfo/policylist'|ezurl()}>{'Policy Functions'|i18n('SysInfo')}</a></div></li>
    <li><div><a href={'/sysinfo/fetchlist'|ezurl()}>{'Fetch Functions'|i18n('SysInfo')}</a></div></li>
    {*<li><div><a href={'/sysinfo/operatorlist'|ezurl()}>{'Template Operators'|i18n('SysInfo')}</a></div></li>*}
</ul>
{* DESIGN: Content END *}</div></div></div></div></div></div>
