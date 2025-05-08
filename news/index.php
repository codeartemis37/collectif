<?php
include("../modules/site.php");

// Configuration des données et récupération du mois courant
$currentYear = intval($_GET['year'] ?? date('Y'));
$currentMonth = intval($_GET['month'] ?? date('m'));

$data = [
    2025 => [
        3 => "[b]Le site est toujours en développement[/b] : [i]Liste des todos disponible à : [url=". $site. "#TODO.txt]Détails techniques[/url][/i]",
        9 => "[b]Loi sur l'économie circulaire[/b] : [color=#00ff00]Réduction des déchets et recyclage[/color]"
    ]
];

// Conversion BBCode vers HTML avec style terminal
function bbcode_to_terminal($text) {
    $replacements = [
        '/\[b\](.*?)\[\/b\]/is' => '<span class="bold">$1</span>',
        '/\[i\](.*?)\[\/i\]/is' => '<span class="italic">$1</span>',
        '/\[url=(.*?)\](.*?)\[\/url\]/is' => '<a href="$1" class="link">$2</a>',
        '/\[color=(.*?)\](.*?)\[\/color\]/is' => '<span style="color: $1">$2</span>',
        '/\[quote\](.*?)\[\/quote\]/is' => '<blockquote>↳ $1</blockquote>'
    ];
    
    return preg_replace(array_keys($replacements), array_values($replacements), $text);
}

$newsContent = bbcode_to_terminal($data[$currentYear][$currentMonth] ?? '[i]Aucune actualité ce mois-ci[/i]');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal des Actualités</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #000;
        }
        .terminal {
            background: #000;
            color: #0f0;
            font-family: 'Courier New', monospace;
            padding: 20px;
            min-height: 300px;
        }
        .bold { font-weight: 700; }
        .italic { font-style: italic; }
        .link { 
            color: #0ff;
            text-decoration: underline;
            cursor: pointer;
        }
        blockquote {
            margin: 10px 0;
            padding-left: 15px;
            border-left: 2px solid #0f0;
        }
        #cursor {
            animation: blink 0.5s infinite;
            font-weight: 700;
        }
        @keyframes blink { 50% { opacity: 0; } }
    </style>
</head>
<body>
    <div class="terminal">
        <div id="output"></div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.execCommand('AutoUrlDetect', false, false);
});
</script>

    <script>
        const content = <?= json_encode($newsContent) ?>;
        const output = document.getElementById('output');

        function createDisplayList(content) {
            const displayList = [];
            let inTag = false;
            let currentTag = '';

            for (let i = 0; i < content.length; i++) {
                if (content[i] === '<') {
                    inTag = true;
                    currentTag = '<';
                } else if (content[i] === '>' && inTag) {
                    currentTag += '>';
                    displayList.push(currentTag);
                    inTag = false;
                    currentTag = '';
                } else if (inTag) {
                    currentTag += content[i];
                } else {
                    displayList.push(content[i]);
                }
            }
            return displayList;
        }

        function typeContent(displayList, index = 0) {
            if (index < displayList.length) {
                output.innerHTML = displayList.slice(0, index).join('') + '<span id="cursor">_</span>';
                setTimeout(() => typeContent(displayList, index + 1), 40);
            }
        }

        const displayList = createDisplayList(content);
        setTimeout(() => typeContent(displayList), 1000);
    </script>
</body>
</html>
