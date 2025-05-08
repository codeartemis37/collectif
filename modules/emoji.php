<?php
// Dictionnaire des émojis
$emojis = [
    ':ironie:' => '&#128530;',
    ':joie:' => '&#129395;',
    ':colere:' => '&#128545;',
    ':chut:' => '&#129323;'
];

function auto_BBCode($text) {
    global $emojis;

    // Remplacement des émojis par des spans avec titres
    foreach ($emojis as $key => $code) {
        $title = substr($key, 1, -1); // Enlève les ':' du début et de la fin
        $text = preg_replace(
            '/' . preg_quote($key, '/') . '/',
            "<span title='{$title}'>{$code}</span>",
            $text
        );
    }

    // Gestion des balises BBCode
    $bbcode = [
        '/\[b\](.*?)\[\/b\]/is',
        '/\[i\](.*?)\[\/i\]/is',
        '/\[u\](.*?)\[\/u\]/is',
        '/\[list\](.*?)\[\/list\]/is',
        '/\[*\](.*?)(?=\[*\]|\[\/list\])/is',
        '/\[url=(.*?)\](.*?)\[\/url\]/is',
        '/\[quote\](.*?)\[\/quote\]/is',
        '/\[color=(.*?)\](.*?)\[\/color\]/is',
        '/\[size=(.*?)\](.*?)\[\/size\]/is'
    ];
    
    $html = [
        '<strong>$1</strong>',
        '<em>$1</em>',
        '<u>$1</u>',
        '<ul>$1</ul>',
        '<li>$1</li>',
        '<a href="$1">$2</a>',
        '<blockquote>$1</blockquote>',
        '<span style="color:$1;">$2</span>',
        '<span style="font-size:$1px;">$2</span>'
    ];
    
    $text = preg_replace($bbcode, $html, $text);

    // Gestion des balises ||
    $text = preg_replace(
        '/\|\|(.*?)\|\|/s',
        '<span class="spoiler" onclick="this.classList.toggle(\'revealed\')">$1</span>',
        $text
    );

    return $text;
}
?>
