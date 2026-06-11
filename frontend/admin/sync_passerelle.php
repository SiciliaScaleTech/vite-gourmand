<?php
header('Content-Type: application/json');
require_once '../../backend/config.php';

// Récupération des dates envoyées en POST par le JavaScript
$data_input = json_decode(file_get_contents('php://input'), true);
$date_debut = $data_input['date_debut'] ?? '';
$date_fin = $data_input['date_fin'] ?? '';

try {
    // 1. SYNCHRONISATION : On cherche les commandes 'terminée' sur la période demandée
    $sql = "
        SELECT 
            c.id as commande_id, c.date_commande, d.quantite, 
            m.nom_technique, m.titre, m.prix_pers
        FROM details_commandes d
        INNER JOIN commandes c ON d.id_commande = c.id
        INNER JOIN menu m ON d.id_menu = m.id
        WHERE c.statut = 'terminée'
    ";
    
    if (!empty($date_debut)) $sql .= " AND DATE(c.date_commande) >= :date_debut";
    if (!empty($date_fin)) $sql .= " AND DATE(c.date_commande) <= :date_fin";

    $query_ventes = $pdo->prepare($sql);
    if (!empty($date_debut)) $query_ventes->bindValue(':date_debut', $date_debut);
    if (!empty($date_fin)) $query_ventes->bindValue(':date_fin', $date_fin);
    
    $query_ventes->execute();
    $ventes_reelles = $query_ventes->fetchAll(PDO::FETCH_ASSOC);

   // Si on trouve des commandes terminées, on les pousse dans MongoDB Atlas
if (!empty($ventes_reelles)) {
    foreach ($ventes_reelles as $vente) {
        $bulk = new MongoDB\Driver\BulkWrite;
        
        // 1. On ne met dans $donnees_initiales que les infos de base du menu
        $donnees_initiales = [
            'code' => $vente['nom_technique'],
            'titre' => $vente['titre'],
            'prix_pers' => (float)$vente['prix_pers']
        ];

        // 2. MongoDB va créer le tableau 'stats.dernieres_commandes' automatiquement s'il n'existe pas
        $bulk->update(
            ['code' => $vente['nom_technique']],
            [
                '$setOnInsert' => $donnees_initiales,
                '$push' => [
                    'stats.dernieres_commandes' => [
                        'date' => date('Y-m-d H:i:s', strtotime($vente['date_commande'])), 
                        'quantite' => (int)$vente['quantite']
                    ]
                ]
            ],
            ['multi' => false, 'upsert' => true]
        );
        $managerMongoDB->executeBulkWrite($collectionMenus, $bulk);

        // Archivage dans MySQL
        $update_sql = $pdo->prepare("UPDATE commandes SET statut = 'Archivée' WHERE id = ?");
        $update_sql->execute([$vente['commande_id']]);
    }
}

    // 2. RÉCUPÉRATION DES DONNÉES FILTRÉES DEPUIS MONGODB POUR LE GRAPHIQUE
    $labels_graphique = [];
    $donnees_graphique = [];
    $chiffre_affaires = 0;

    $query_mongo = new MongoDB\Driver\Query([]);
    $cursor = $managerMongoDB->executeQuery($collectionMenus, $query_mongo);

    foreach ($cursor as $menu) {
        $menuData = (array)$menu;
        $nom_menu = $menuData['titre'] ?? '';
        $prix_unitaire = (float)($menuData['prix_pers'] ?? 0);
        $ventes_menu_periode = 0;

        if (isset($menuData['stats'])) {
            $stats = (array)$menuData['stats'];
            if (isset($stats['dernieres_commandes'])) {
                foreach ((array)$stats['dernieres_commandes'] as $commande) {
                    $cmdData = (array)$commande;
                    $date_commande = date('Y-m-d', strtotime($cmdData['date'] ?? ''));
                    $quantite = (int)($cmdData['quantite'] ?? 1);

                    // Filtrage dynamique NoSQL
                    if (!empty($date_debut) && $date_commande < $date_debut) continue;
                    if (!empty($date_fin) && $date_commande > $date_fin) continue;

                    $ventes_menu_periode += $quantite;
                    $chiffre_affaires += ($prix_unitaire * $quantite);
                }
            }
        }

        if ($ventes_menu_periode > 0) {
            $labels_graphique[] = $nom_menu;
            $donnees_graphique[] = $ventes_menu_periode;
        }
    }

    // On renvoie tout le bilan au JavaScript
    echo json_encode([
        'status' => 'success',
        'labels' => $labels_graphique,
        'donnees' => $donnees_graphique,
        'ca' => number_format($chiffre_affaires, 2, ',', ' ') . ' €'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}