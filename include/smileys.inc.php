<?php

function addSmileys($content) {
	$content = eregi_replace(quotemeta(":)"), "<img src='graphics/smileys/smile.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":("), "<img src='graphics/smileys/sad.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":D"), "<img src='graphics/smileys/biggrin.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":'("), "<img src='graphics/smileys/cry.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":o"), "<img src='graphics/smileys/bigeek.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(";)"), "<img src='graphics/smileys/wink.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta("(y)"), "<img src='graphics/smileys/yes.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta("(n)"), "<img src='graphics/smileys/no.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":p"), "<img src='graphics/smileys/bigrazz.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":@"), "<img src='graphics/smileys/mad.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":s"), "<img src='graphics/smileys/none.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":x"), "<img src='graphics/smileys/dead.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":b"), "<img src='graphics/smileys/cool.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":h"), "<img src='graphics/smileys/laugh.gif' border='0' />", $content);
	$content = eregi_replace(quotemeta(":r"), "<img src='graphics/smileys/rolleyes.gif' border='0' />", $content);

	return $content;
}

?>
