<?php

namespace Model;
class Categorie
{

    public static function findAll(): array
    {
        global $pdo;
        return $pdo->query("SELECT * FROM web2026_Categorie ORDER BY cid ASC")->fetchAll();
    }
}
