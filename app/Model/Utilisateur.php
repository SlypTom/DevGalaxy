<?php

namespace Model;
class Utilisateur
{

    public static function findById(int $uid)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM web2026_Utilisateur WHERE uid = :uid");
        $stmt->execute(['uid' => $uid]);
        return $stmt->fetch();
    }

    public static function findByEmail(string $email)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM web2026_Utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public static function findAllArtistes(): array
    {
        global $pdo;
        return $pdo->query("
            SELECT * FROM web2026_Utilisateur
            WHERE est_organisateur = 0
            ORDER BY nom, prenom ASC
        ")->fetchAll();
    }

    public static function findArtistesProgrammes(): array
    {
        global $pdo;
        return $pdo->query("
            SELECT DISTINCT u.*
            FROM web2026_Utilisateur u
            INNER JOIN web2026_Prestation p   ON p.artiste_id = u.uid
            INNER JOIN web2026_Programmation prog ON prog.prestation_id = p.pid
            WHERE u.est_organisateur = 0
        ")->fetchAll();
    }

    public static function findAllWithStats(): array
    {
        global $pdo;
        return $pdo->query("
            SELECT u.uid, u.nom, u.prenom, u.nom_artiste,
                   COUNT(DISTINCT p.pid)         AS nb_prestations,
                   COUNT(DISTINCT prog.prog_id)  AS nb_programmes
            FROM web2026_Utilisateur u
            LEFT JOIN web2026_Prestation p       ON p.artiste_id = u.uid
            LEFT JOIN web2026_Programmation prog ON prog.prestation_id = p.pid
            WHERE u.est_organisateur = 0
            GROUP BY u.uid, u.nom, u.prenom, u.nom_artiste
            ORDER BY u.nom, u.prenom ASC
        ")->fetchAll();
    }

    public static function emailExists(string $email, int $excludeUid = 0): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM web2026_Utilisateur
            WHERE email = :email AND uid != :uid
        ");
        $stmt->execute(['email' => $email, 'uid' => $excludeUid]);
        return $stmt->fetchColumn() > 0;
    }

    public static function create(string  $nom, string $prenom, string $nomArtiste,
                                  string  $email, string $mdp, string $description,
                                  ?string $photo = null): int
    {
        global $pdo;
        // N'insère la photo que si la colonne existe et qu'une photo est fournie
        if ($photo !== null) {
            $stmt = $pdo->prepare("
                INSERT INTO web2026_Utilisateur
                    (nom, prenom, nom_artiste, email, mot_passe_hashe, description, est_organisateur, photo)
                VALUES
                    (:nom, :prenom, :pseudo, :email, :mdp, :desc, 0, :photo)
            ");
            $stmt->execute([
                'nom' => $nom, 'prenom' => $prenom, 'pseudo' => $nomArtiste,
                'email' => $email, 'mdp' => $mdp, 'desc' => $description,
                'photo' => $photo,
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO web2026_Utilisateur
                    (nom, prenom, nom_artiste, email, mot_passe_hashe, description, est_organisateur)
                VALUES
                    (:nom, :prenom, :pseudo, :email, :mdp, :desc, 0)
            ");
            $stmt->execute([
                'nom' => $nom, 'prenom' => $prenom, 'pseudo' => $nomArtiste,
                'email' => $email, 'mdp' => $mdp, 'desc' => $description,
            ]);
        }
        return (int)$pdo->lastInsertId();
    }

    public static function update(int     $uid, string $nom, string $prenom, string $nomArtiste,
                                  string  $email, string $description,
                                  ?string $mdp = null, ?string $photo = null): bool
    {
        global $pdo;
        $params = [
            'nom' => $nom,
            'prenom' => $prenom,
            'na' => $nomArtiste,
            'email' => $email,
            'desc' => $description,
            'uid' => $uid,
        ];
        $extra = '';
        if ($mdp !== null) {
            $extra .= ', mot_passe_hashe = :mdp';
            $params['mdp'] = $mdp;
        }
        if ($photo !== null) {
            $extra .= ', photo = :photo';
            $params['photo'] = $photo;
        }
        $stmt = $pdo->prepare("
            UPDATE web2026_Utilisateur
            SET nom = :nom, prenom = :prenom, nom_artiste = :na,
                email = :email, description = :desc $extra
            WHERE uid = :uid
        ");
        return $stmt->execute($params);
    }

    public static function delete(int $uid): bool
    {
        global $pdo;
        try {
            $pdo->beginTransaction();
            $pdo->prepare("
                DELETE prog FROM web2026_Programmation prog
                JOIN web2026_Prestation pr ON prog.prestation_id = pr.pid
                WHERE pr.artiste_id = :uid
            ")->execute(['uid' => $uid]);
            $pdo->prepare("DELETE FROM web2026_Prestation WHERE artiste_id = :uid")
                ->execute(['uid' => $uid]);
            $pdo->prepare("DELETE FROM web2026_Utilisateur WHERE uid = :uid AND est_organisateur = 0")
                ->execute(['uid' => $uid]);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public static function getNomAffichage(array $user): string
    {
        return !empty($user['nom_artiste']) ? $user['nom_artiste'] : $user['prenom'] . ' ' . $user['nom'];
    }

    public static function getPhotoUrl(array $user): string
    {
        $photo = $user['photo'] ?? null;
        if (!empty($photo) && file_exists(__DIR__ . '/../img/profil/' . $photo)) {
            return 'img/profil/' . $photo;
        }
        return '';
    }
}
