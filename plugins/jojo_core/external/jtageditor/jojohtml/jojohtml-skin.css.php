<?php
header('Content-type: text/css');
header('Cache-Control: private, max-age=28800');
header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
header('Pragma: ');

?>/* -------------------------------------------------------------------
// Html Editor Skin 
// j(Universal?)TagEditor, JQuery plugin
// By Jay Salvat - http://www.jaysalvat.com/jquery/jtageditor/
// -------------------------------------------------------------------
// Icons based on http://www.famfamfam.com/
// ------------------------------------------------------------------*/
.jTagHTML {

}
.jTagHTML .jTagEditor-editor {
	font:12px "Courier New", Courier, monospace;
	padding:8px;
	margin-top:10px;
	width:100%;
	height:320px;
	clear:both;
	display:block;
	line-height:18px;
}
.jTagHTML .jTagEditor-toolBar {
	list-style:none;
}
.jTagHTML .jTagEditor-toolBar ul	{
	margin:0px; padding:0px;
}
.jTagHTML .jTagEditor-toolBar li	{
	float:left;
	margin: 0;
	margin-bottom:5px;
}
.jTagHTML .jTagEditor-toolBar a	{
	display:block;
	width:16px; height:16px;
	margin:2px;
	text-indent:-1000px;
	overflow:hidden;
}
.jTagHTML .jTagEditor-button1 a	{
	background-image:url(../_icons/h1.png); 
}
.jTagHTML .jTagEditor-button2 a	{
	background-image:url(../_icons/h2.png); 
}
.jTagHTML .jTagEditor-button3 a	{
	background-image:url(../_icons/h3.png); 
}
.jTagHTML .jTagEditor-button4 a	{
	background-image:url(../_icons/h4.png); 
}
.jTagHTML .jTagEditor-button5 a	{
	background-image:url(../_icons/h5.png); 
}
.jTagHTML .jTagEditor-button6 a	{
	background-image:url(../_icons/h6.png); 
}
.jTagHTML .jTagEditor-button7 a	{
	background-image:url(../_icons/paragraph.png); 
	margin-right:12px;
}
.jTagHTML .jTagEditor-button8 a	{
	background-image:url(../_icons/picture.png); 
}
.jTagHTML .jTagEditor-button9 a	{
	background-image:url(../_icons/link.png);
	margin-right:12px;
}
.jTagHTML .jTagEditor-button10 a	{
	background-image:url(../_icons/bold.png);
}
.jTagHTML .jTagEditor-button11 a	{
	background-image:url(../_icons/italic.png);
}
.jTagHTML .jTagEditor-button12 a	{
	background-image:url(../_icons/stroke.png);
}
.jTagHTML .jTagEditor-button13 a	{
	background-image:url(../_icons/superscript.png);
}
.jTagHTML .jTagEditor-button14 a	{
	background-image:url(../_icons/subscript.png);
	margin-right:12px;
}
.jTagHTML .jTagEditor-button15 a	{
	background-image:url(../_icons/table.png);
}
.jTagHTML .jTagEditor-button16 a	{
	background-image:url(../_icons/table-row.png);
}
.jTagHTML .jTagEditor-button17 a	{
	background-image:url(../_icons/table-col.png);
	margin-right:12px;
}
.jTagHTML .jTagEditor-button18 a	{
	background-image:url(../_icons/list-bullets.png);
}
.jTagHTML .jTagEditor-button19 a	{
	background-image:url(../_icons/list-numbers.png);
}
.jTagHTML .jTagEditor-button20 a	{
	background-image:url(../_icons/list-item.png);
}
.jTagHTML .jTagEditor-button21 a	{
	background-image:url(../_icons/indent.png);
	margin-right:12px;
}
.jTagHTML .jTagEditor-button22 a	{
	background-image:url(../_icons/code.png);
}
.jTagHTML .jTagEditor-button23 a	{
	background-image:url(../_icons/comments.png);
	margin-right:12px;
}
.jTagHTML .jTagEditor-button24 a	{
	background-image:url(../_icons/tags-close.png);
}
.jTagHTML .jTagEditor-button25 a	{
	background-image:url(../_icons/tags-delete.png);
}
.jTagHTML .jTagEditor-button26 a	{
	background-image:url(../_icons/preview.png);
}

<?php

$contentvars = Jojo::getContentVars();
$i = 27;
foreach ($contentvars as $v) {
echo ".jTagHTML .jTagEditor-button".$i++." a {
	background-image:url(../../../".$v['icon'].");
}
";
}
?>

.jTagHTML .jTagEditor-resizeHandle {
	width:22px; height:6px;
	margin:5px 0 0 394px;
	background-image:url(../_images/handle.png);
	cursor:n-resize;
}