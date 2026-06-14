<?php

namespace Model;
class Scene
{

    public static function findAll(): array
    {
        global $pdo;
        return $pdo->query("SELECT * FROM web2026_Scene ORDER BY sid ASC")->fetchAll();
    }

    public static function findLibres(string $heure): array
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT * FROM web2026_Scene
            WHERE sid NOT IN (
                SELECT scene_id FROM web2026_Programmation WHERE heure_debut = :h
            )
            ORDER BY sid ASC
        ");
        $stmt->execute(['h' => $heure]);
        return $stmt->fetchAll();
    }
}
