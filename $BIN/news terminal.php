<?php
// Configuration des données et récupération du mois courant
$currentYear = date('Y');
$currentMonth = date('n');

$data = [
    2025 => [
        3 => "[b]le site est toujours en developpement[/b] : [i]liste des todos disponible à: [url=https://example.com]Détails techniques[/url][/i]",
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
    
    return preg_replace(array_keys($replacements), $replacements, $text);
}

$newsContent = bbcode_to_terminal($data[$currentYear][$currentMonth] ?? '[i]Aucune actualité ce mois-ci[/i]');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Terminal des Actualités</title>
    <style>
        .terminal {
            background: #000;
            color: #0f0;
            font-family: 'Courier New', monospace;
            padding: 20px;
            min-height: 300px;
        }
        .bold { font-weight: 700 }
        .italic { font-style: italic }
        .link { 
            color: #0ff;
            text-decoration: underline;
            cursor: pointer;
        }
        blockquote {
            margin: 10px 0;
            padding-left: 15px;
            border-left: 2px solid #0f03;
        }
        #cursor {
            animation: blink 1s infinite;
            font-weight: 700;
        }
        @keyframes blink { 50% { opacity: 0 } }
    </style>
</head>
<body>
    <div class="terminal">
        <div id="output"></div>
        <span id="cursor">_</span>
    </div>

    <script>
        const content = <?= json_encode($newsContent) ?>;
        let index = 0;
        const output = document.getElementById('output');
        
        function typeCharacter() {
            if(index < content.length) {
                output.innerHTML += content[index++];
                setTimeout(typeCharacter, 40);
            } else {
                document.getElementById('cursor').style.display = 'none';
            }
        }
        
        setTimeout(typeCharacter, 1000);
    </script>
</body>
</html>
