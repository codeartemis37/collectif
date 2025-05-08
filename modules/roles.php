<?php
require_once('site.php');

$roles = [
    'visiteur' => [],
    'membre' => [],
    'moderateur' => ['artemis37', 'system', 'bot'],
    'admin' => [],
    'superadmin' => []
];

function a_le_role($utilisateur, $role) {
    global $roles;
    foreach ($roles as $nom_role => $utilisateurs) {
        if (in_array($utilisateur, $utilisateurs)) {
            return $nom_role === $role || array_search($nom_role, array_keys($roles)) >= array_search($role, array_keys($roles));
        }
    }
    return $role === 'visiteur';
}

function plus_grand_role_de($utilisateur) {
    global $roles;
    foreach (array_reverse($roles) as $nom_role => $utilisateurs) {
        if (in_array($utilisateur, $utilisateurs)) {
            return $nom_role;
        }
    }
    return 'visiteur';
}


?>