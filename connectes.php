<?php
require_once('modules/site.php');
require_once('modules/roles.php');

// Fonction pour récupérer les utilisateurs connectés
function getConnectedUsers($site, $forum) {
    $apiUrl = $site . "modules/api.php?action=get_liste_connectes&context=" . urlencode($forum);
    return json_decode(file_get_contents($apiUrl), true);
}

// Récupération des données
$data = getConnectedUsers($site, $_GET['forum']);

// Affichage des utilisateurs connectés
?>
<div class="div-connectes">
    <h2 class="h2-connectes">Utilisateurs connectés ayant envoyé un message au cours de ces 2 dernieres minutes</h2>
    <ul class="ul-connectes">
    <?php if (isset($data['error'])): ?>
        <li class="li-connectes error-connectes"><?= htmlspecialchars($data['error']) ?></li>
    <?php elseif (!empty($data['connected_users'])): ?>
        <?php foreach ($data['connected_users'] as $user): ?>
            <li class="li-connectes">
                <span class="pastille-connectes" title="<?= htmlspecialchars(plus_grand_role_de($user)) ?>"></span>
                <span><?= htmlspecialchars($user) ?></span>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="li-connectes">Aucun utilisateur connecté.</li>
    <?php endif; ?>
    </ul>
</div>