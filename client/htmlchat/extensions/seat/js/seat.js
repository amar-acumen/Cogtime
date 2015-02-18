//Microsoft.Glimmer.OneWay
//<AnimationCollection FilePath="G:\myprojects\tribe2\glimmer\Glimmer\glimmerUI\glimmerUI\samples\js\tooltip.html.glimmer.js" xmlns="clr-namespace:GlimmerLib;assembly=GlimmerLib" xmlns:x="http://schemas.microsoft.com/winfx/2006/xaml"><Animation Name="mouseoverActiontooltip" EventType="mouseover" Trigger=".tooltip"><Animation.Targets><Target Name="#tooltip" Duration="1000" Easing="linear" Callback="null"><Target.Effects><AppendHTMLEffect CSSName="body" DisplayName="Append HTML" MaxValue="0" MinValue="0" From="0" To="&lt;p id='tooltip'&gt;&quot;+ this.rel + &quot;&lt;/p&gt;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="False" FormatString="$({1}).append({2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /><ModifyCSSEffect CSSName="left" DisplayName="ModifyCSS" MaxValue="5000" MinValue="-5000" From="0" To="(event.pageX + 20) + &quot;px&quot;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="True" FormatString="$({0}).css({1},{2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /><ModifyCSSEffect CSSName="top" DisplayName="ModifyCSS" MaxValue="5000" MinValue="-5000" From="0" To="(event.pageY - 10) + &quot;px&quot;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="True" FormatString="$({0}).css({1},{2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /></Target.Effects></Target></Animation.Targets></Animation><Animation Name="mouseoutActiontooltip" EventType="mouseout" Trigger=".tooltip"><Animation.Targets><Target Name="{x:Null}" Duration="1000" Easing="linear" Callback="null"><Target.Effects><RemoveHTMLEffect CSSName="#tooltip" DisplayName="Remove HTML" MaxValue="0" MinValue="0" From="0" To="0" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="False" FormatString="$({1}).remove();&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /></Target.Effects></Target></Animation.Targets></Animation><Animation Name="mousemoveActiontooltip" EventType="mousemove" Trigger=".tooltip"><Animation.Targets><Target Name="#tooltip" Duration="1000" Easing="linear" Callback="null"><Target.Effects><ModifyCSSEffect CSSName="left" DisplayName="ModifyCSS" MaxValue="5000" MinValue="-5000" From="0" To="(event.pageX + 20) + &quot;px&quot;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="True" FormatString="$({0}).css({1},{2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /><ModifyCSSEffect CSSName="top" DisplayName="ModifyCSS" MaxValue="5000" MinValue="-5000" From="0" To="(event.pageY - 10) + &quot;px&quot;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="True" FormatString="$({0}).css({1},{2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /></Target.Effects></Target></Animation.Targets></Animation><Animation Name="mouseoverActiontooltipImage" EventType="mouseover" Trigger=".tooltipImage"><Animation.Targets><Target Name="#tooltip" Duration="1000" Easing="linear" Callback="null"><Target.Effects><AppendHTMLEffect CSSName="body" DisplayName="Append HTML" MaxValue="0" MinValue="0" From="0" To="&lt;p id='tooltip'&gt;&lt;img src=&quot;+ this.rel + &quot;&gt;&lt;/img&gt;&lt;/p&gt;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="False" FormatString="$({1}).append({2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /><ModifyCSSEffect CSSName="left" DisplayName="ModifyCSS" MaxValue="5000" MinValue="-5000" From="0" To="(event.pageX + 20) + &quot;px&quot;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="True" FormatString="$({0}).css({1},{2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /><ModifyCSSEffect CSSName="top" DisplayName="ModifyCSS" MaxValue="5000" MinValue="-5000" From="0" To="(event.pageY - 10) + &quot;px&quot;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="True" FormatString="$({0}).css({1},{2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /></Target.Effects></Target></Animation.Targets></Animation><Animation Name="mouseoutActiontooltipImage" EventType="mouseout" Trigger=".tooltipImage"><Animation.Targets><Target Name="{x:Null}" Duration="1000" Easing="linear" Callback="null"><Target.Effects><RemoveHTMLEffect CSSName="#tooltip" DisplayName="Remove HTML" MaxValue="0" MinValue="0" From="0" To="0" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="False" FormatString="$({1}).remove();&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /></Target.Effects></Target></Animation.Targets></Animation><Animation Name="mousemoveActiontooltipImage" EventType="mousemove" Trigger=".tooltipImage"><Animation.Targets><Target Name="#tooltip" Duration="1000" Easing="linear" Callback="null"><Target.Effects><ModifyCSSEffect CSSName="left" DisplayName="ModifyCSS" MaxValue="5000" MinValue="-5000" From="0" To="(event.pageX + 20) + &quot;px&quot;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="True" FormatString="$({0}).css({1},{2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /><ModifyCSSEffect CSSName="top" DisplayName="ModifyCSS" MaxValue="5000" MinValue="-5000" From="0" To="(event.pageY - 10) + &quot;px&quot;" IsStartValue="False" IsActive="True" IsAnimatable="False" IsExpression="True" FormatString="$({0}).css({1},{2});&#xD;&#xA;" RequiresJQueryPlugin="False" JQueryPluginURI="" /></Target.Effects></Target></Animation.Targets></Animation></AnimationCollection>
jQuery(function($) {
var timer;
var offsetY = 10;
var offsetX = -20;
function mouseoverActiontooltip(event)
{
     if ($(this).attr("type") == undefined || $(this).attr("type") == null || $(this).attr("type") == 'normal')
     {
         return;
     }
     if (this.rel != undefined)
     {
         $("body").append("<span id='tooltip'>"+ this.rel + "</span>");
     }
     else
     {
         $("body").append("<span id='tooltip'>"+ $(this).text() + "</span>");
     }
     if ($(this).attr("type") == 'tipName')
     {
         offsetX = -40;
         offsetY = -50;
     }
     else
     {
         offsetY = 10;
     }
     if (event.pageX >= 314)
     {
         $("#tooltip").css("left", "314px");
     }
     else
     {
         $("#tooltip").css("left",(event.pageX + offsetX) + "px");
     }    
     $("#tooltip").css("top",(event.pageY + offsetY) + "px");
}

function mouseoutActiontooltip(event)
{
     $("#tooltip").remove();
}

function mousemoveActiontooltip(event)
{
    if (event.pageX >= 314)
    {
        $("#tooltip").css("left", "305px");
    }
    else
    {
        $("#tooltip").css("left",(event.pageX + offsetX) + "px");
    }
    $("#tooltip").css("top",(event.pageY + offsetY) + "px");
}

function mouseoverActiontooltipImage(event)
{
     $("body").append("<p id='tooltip'><img src="+ this.rel + "></img></p>");
     $("#tooltip").css("left",(event.pageX + offsetX) + "px");
     $("#tooltip").css("top",(event.pageY + offsetY) + "px");
}

function mouseoutActiontooltipImage(event)
{
     $("#tooltip").remove();
}

function mousemoveActiontooltipImage(event)
{
     $("#tooltip").css("left",(event.pageX + offsetX) + "px");
     $("#tooltip").css("top",(event.pageY + offsetY) + "px");
}

$('.tooltip').bind('mouseover', mouseoverActiontooltip);

$('.tooltip').bind('mouseout', mouseoutActiontooltip);

$('.tooltip').bind('mousemove', mousemoveActiontooltip);

$('.tooltipImage').bind('mouseover', mouseoverActiontooltipImage);

$('.tooltipImage').bind('mouseout', mouseoutActiontooltipImage);

$('.tooltipImage').bind('mousemove', mousemoveActiontooltipImage);

});