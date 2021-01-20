<?php

function addSmileys($content)
{
    return str_replace(
        [
            ':)',
            ':(',
            ':D',
            ":'(",
            ':o',
            ';)',
            '(y)',
            '(n)',
            ':p',
            ':@',
            ':s',
            ':x',
            ':b',
            ':h',
            ':r',
        ],
        [
            "<img src='graphics/smileys/smile.gif' border='0' />",
            "<img src='graphics/smileys/sad.gif' border='0' />",
            "<img src='graphics/smileys/biggrin.gif' border='0' />",
            "<img src='graphics/smileys/cry.gif' border='0' />",
            "<img src='graphics/smileys/bigeek.gif' border='0' />",
            "<img src='graphics/smileys/wink.gif' border='0' />",
            "<img src='graphics/smileys/yes.gif' border='0' />",
            "<img src='graphics/smileys/no.gif' border='0' />",
            "<img src='graphics/smileys/bigrazz.gif' border='0' />",
            "<img src='graphics/smileys/mad.gif' border='0' />",
            "<img src='graphics/smileys/none.gif' border='0' />",
            "<img src='graphics/smileys/dead.gif' border='0' />",
            "<img src='graphics/smileys/cool.gif' border='0' />",
            "<img src='graphics/smileys/laugh.gif' border='0' />",
            "<img src='graphics/smileys/rolleyes.gif' border='0' />",
        ],
        $content
    );
}