/**************************************************************
 *        M A X   J A V A S C R I P T   T O O L T I P
 *       
 * Script name: Max's JavaScript Tooltip
 * 
 * Version: 1.0
 * Release date: 2008-01-30
 *
 * Warranty: No warranty! Use it for your own risk!
 *
 ***************************************************************/
 
Installation:

1. Import the tooltip.js into your HTML code in the head section like this:
         <script type="text/javascript" src="tooltip.js"></script>
2. Add an empty div tag with id "toolTip" to your site.
         <div id="toolTip"> </div>
3. Add an onmouseover and onmouseout event to any HTML element which support it.
         <td onmouseover="showToolTip('Name','Please enter your name here.',event);" onmouseout="hideToolTip();">Name:</td>   
4. Edit your CSS file and customize the look and feel as you want.
         
That's all!         