<?php

namespace Model;
class Programmation
{

    public static function findAll(): array
    {
        global $pdo;
        return $pdo->query("
            SELECT prog.prog_id, prog.heure_debut, prog.scene_id,
                   pr.pid, pr.intitule AS mission,
                   u.nom_artiste, u.prenom, u.nom
            FROM web2026_Programmation prog
            JOIN web2026_Prestation pr  ON prog.prestation_id = pr.pid
            JOIN web2026_Utilisateur u  ON pr.artiste_id = u.uid
            ORDER BY prog.heure_debut ASC
        ")->fetchAll();
    }

    public static function findByArtiste(int $artisteId): array
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT p.heure_debut, pr.intitule, s.nom_scene
            FROM web2026_Programmation p
            JOIN web2026_Prestation pr ON p.prestation_id = pr.pid
            JOIN web2026_Scene s       ON p.scene_id = s.sid
            WHERE pr.artiste_id = :uid
            ORDER BY p.heure_debut ASC
        ");
        $stmt->execute(['uid' => $artisteId]);
        return $stmt->fetchAll();
    }

    public static function buildGrille(): array
    {
        $grille = [];
        foreach (self::findAll() as $event) {
            $heure = substr($event['heure_debut'], 0, 5);
            if (!isset($grille[$heure])) $grille[$heure] = [];
            $grille[$heure][$event['scene_id']] = $event;
        }
        return $grille;
    }

    public static function hasCreneauDispo(array $heuresPossibles, int $nbScenes): bool
    {
        global $pdo;
        foreach ($heuresPossibles as $h) {
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT scene_id) FROM web2026_Programmation WHERE heure_debut = :h");
            $stmt->execute(['h' => $h]);
            if ($stmt->fetchColumn() < $nbScenes) return true;
        }
        return false;
    }

    public static function getHeuresDisponibles(array $heuresPossibles, int $nbScenes): array
    {
        global $pdo;
        $dispo = [];
        foreach ($heuresPossibles as $h) {
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT scene_id) FROM web2026_Programmation WHERE heure_debut = :h");
            $stmt->execute(['h' => $h]);
            if ($stmt->fetchColumn() < $nbScenes) $dispo[] = $h;
        }
        return $dispo;
    }

    public static function sceneOccupee(int $sceneId, string $heure): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM web2026_Programmation WHERE scene_id = :sid AND heure_debut = :h");
        $stmt->execute(['sid' => $sceneId, 'h' => $heure]);
        return $stmt->fetchColumn() > 0;
    }

    public static function artisteOccupe(int $artisteId, string $heure): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM web2026_Programmation p
            JOIN web2026_Prestation pr ON p.prestation_id = pr.pid
            WHERE pr.artiste_id = :aid AND p.heure_debut = :h
        ");
        $stmt->execute(['aid' => $artisteId, 'h' => $heure]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getTitreByProgId(int $progId): string
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT pr.intitule FROM web2026_Programmation prog
            JOIN web2026_Prestation pr ON prog.prestation_id = pr.pid
            WHERE prog.prog_id = :prog_id
        ");
        $stmt->execute(['prog_id' => $progId]);
        $row = $stmt->fetch();
        return $row ? $row['intitule'] : '';
    }

    public static function create(int $prestationId, int $sceneId, string $heure): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO web2026_Programmation (prestation_id, scene_id, heure_debut)
            VALUES (:pid, :sid, :h)
        ");
        return $stmt->execute(['pid' => $prestationId, 'sid' => $sceneId, 'h' => $heure]);
    }

    public static function delete(int $progId): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM web2026_Programmation WHERE prog_id = :prog_id");
        return $stmt->execute(['prog_id' => $progId]);
    }

    public static function getArtistesLibres(string $heure): array
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT * FROM web2026_Utilisateur u
            WHERE u.est_organisateur = 0 || u.est_organisateur = 1
            AND u.uid NOT IN (
                SELECT pr.artiste_id
                FROM web2026_Programmation p
                JOIN web2026_Prestation pr ON p.prestation_id = pr.pid
                WHERE p.heure_debut = :h
            )
            ORDER BY u.nom_artiste, u.nom ASC
        ");
        $stmt->execute(['h' => $heure]);
        return $stmt->fetchAll();
    }

    public static function getPrestationsByArtiste(int $artisteId): array
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT pid, intitule FROM web2026_Prestation
            WHERE artiste_id = :aid
            ORDER BY intitule ASC
        ");
        $stmt->execute(['aid' => $artisteId]);
        return $stmt->fetchAll();
    }

    public static function getNomArtiste(int $artisteId): string
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT nom_artiste, prenom, nom FROM web2026_Utilisateur WHERE uid = :aid");
        $stmt->execute(['aid' => $artisteId]);
        $row = $stmt->fetch();
        if (!$row) return '';
        return !empty($row['nom_artiste']) ? $row['nom_artiste'] : $row['prenom'] . ' ' . $row['nom'];
    }

    public static function getNomScene(int $sceneId): string
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT nom_scene FROM web2026_Scene WHERE sid = :sid");
        $stmt->execute(['sid' => $sceneId]);
        $row = $stmt->fetch();
        return $row ? $row['nom_scene'] : '';
    }
}
