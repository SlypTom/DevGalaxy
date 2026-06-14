<?php

namespace Model;
class Prestation
{

    public static function findById(int $pid)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT p.*, c.intitule AS nom_categorie,
                   u.uid AS artiste_id, u.nom_artiste, u.prenom, u.nom, u.photo AS photo_artiste,
                   prog.heure_debut, s.nom_scene
            FROM web2026_Prestation p
            JOIN web2026_Categorie c  ON p.categorie_id = c.cid
            JOIN web2026_Utilisateur u ON p.artiste_id = u.uid
            LEFT JOIN web2026_Programmation prog ON p.pid = prog.prestation_id
            LEFT JOIN web2026_Scene s             ON prog.scene_id = s.sid
            WHERE p.pid = :pid
        ");
        $stmt->execute(['pid' => $pid]);
        return $stmt->fetch();
    }

    public static function findByArtiste(int $artisteId): array
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT p.*, c.intitule AS nom_categorie, prog.heure_debut, s.nom_scene
            FROM web2026_Prestation p
            JOIN web2026_Categorie c ON p.categorie_id = c.cid
            LEFT JOIN web2026_Programmation prog ON p.pid = prog.prestation_id
            LEFT JOIN web2026_Scene s             ON prog.scene_id = s.sid
            WHERE p.artiste_id = :id
            ORDER BY p.intitule ASC
        ");
        $stmt->execute(['id' => $artisteId]);
        return $stmt->fetchAll();
    }

    public static function findByArtisteSimple(int $artisteId): array
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT pid, intitule FROM web2026_Prestation WHERE artiste_id = :aid ORDER BY intitule ASC");
        $stmt->execute(['aid' => $artisteId]);
        return $stmt->fetchAll();
    }

    public static function findWithFilters(string $search, string $artisteId,
                                           string $categorieId, bool $programmeesOnly): array
    {
        global $pdo;
        $sql = "
            SELECT p.*, c.intitule AS nom_categorie, u.nom_artiste, u.prenom, u.nom,
                   prog.heure_debut, s.nom_scene
            FROM web2026_Prestation p
            JOIN web2026_Categorie c  ON p.categorie_id = c.cid
            JOIN web2026_Utilisateur u ON p.artiste_id = u.uid
            LEFT JOIN web2026_Programmation prog ON p.pid = prog.prestation_id
            LEFT JOIN web2026_Scene s             ON prog.scene_id = s.sid
            WHERE 1=1
        ";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (p.intitule LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        if (!empty($artisteId)) {
            $sql .= " AND p.artiste_id = :artiste_id";
            $params['artiste_id'] = $artisteId;
        }
        if (!empty($categorieId)) {
            $sql .= " AND p.categorie_id = :categorie_id";
            $params['categorie_id'] = $categorieId;
        }
        if ($programmeesOnly) {
            $sql .= " AND prog.prog_id IS NOT NULL";
        }
        $sql .= " ORDER BY p.intitule ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function isProgrammee(int $pid): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM web2026_Programmation WHERE prestation_id = :pid");
        $stmt->execute(['pid' => $pid]);
        return $stmt->fetchColumn() > 0;
    }

    public static function belongsTo(int $pid, int $artisteId): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM web2026_Prestation WHERE pid = :pid AND artiste_id = :uid");
        $stmt->execute(['pid' => $pid, 'uid' => $artisteId]);
        return $stmt->fetchColumn() > 0;
    }

    public static function create(string $titre, string $description, string $image,
                                  int    $categorieId, int $artisteId): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO web2026_Prestation (intitule, description, image, categorie_id, artiste_id)
            VALUES (:titre, :desc, :image, :cat, :uid)
        ");
        return $stmt->execute([
            'titre' => $titre, 'desc' => $description,
            'image' => $image, 'cat' => $categorieId, 'uid' => $artisteId,
        ]);
    }

    public static function update(int    $pid, int $artisteId, string $titre,
                                  string $description, int $categorieId,
                                  string $image = null): bool
    {
        global $pdo;
        if ($image !== null) {
            $stmt = $pdo->prepare("
                UPDATE web2026_Prestation
                SET intitule = :t, description = :d, categorie_id = :c, image = :img
                WHERE pid = :pid AND artiste_id = :uid
            ");
            return $stmt->execute(['t' => $titre, 'd' => $description, 'c' => $categorieId, 'img' => $image, 'pid' => $pid, 'uid' => $artisteId]);
        }
        $stmt = $pdo->prepare("
            UPDATE web2026_Prestation
            SET intitule = :t, description = :d, categorie_id = :c
            WHERE pid = :pid AND artiste_id = :uid
        ");
        return $stmt->execute(['t' => $titre, 'd' => $description, 'c' => $categorieId, 'pid' => $pid, 'uid' => $artisteId]);
    }

    public static function delete(int $pid, int $artisteId): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM web2026_Prestation WHERE pid = :pid AND artiste_id = :uid");
        return $stmt->execute(['pid' => $pid, 'uid' => $artisteId]);
    }

    public static function getImageUrl(array $presta): string
    {
        if (!empty($presta['image']) && $presta['image'] !== 'default.png'
            && file_exists(__DIR__ . '/../img/prestations/' . $presta['image'])) {
            return 'img/prestations/' . $presta['image'];
        }
        return '';
    }
}
