<?php
declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Rezept {
    public int $id = 0;
    public int $userid = 0;
    public string $title = "";
    public string $desc = "";
    public string $create_dz = "";

    public array $tasks = [];
    public array $zutaten = [];
    public array $kategorien = [];

    public static function getRezeptById(int $id): Rezept {
        if ($id > 0) {
            $sql = sprintf("SELECT * FROM `tab_rezepte` where `pk`= %d;"
                , $id
            );
            return self::getRezeptFromDatabase($sql);
        }
        return new Rezept();
    }

    public static function save(Rezept $rezept):int {
        if ($rezept->id > 0) {
            return self::update($rezept);
        }
        return self::insert($rezept);
    }

    private static function insert(Rezept $rezept):int {
        $sql = sprintf("INSERT INTO `tab_rezepte` "
            . "SET `title`='%s',"
            . "`description`='%s',"
            . "`createdz`=now(),"
            . "`fs_benutzer`=%d;"
            , $rezept->title
            , $rezept->desc
            , $rezept->userid
        );

        $rezept->id = insert($sql);
        Kategorie::saveKategorieForRezept($rezept->id, $rezept->kategorien);
        Zutat::saveZutatenForRezept($rezept->id, $rezept->zutaten);
        Zubereitung::saveZubereitungForRezept($rezept->id, $rezept->tasks);
        return $rezept->id;
    }

    private static function update(Rezept $rezept):int {
//        $sql = sprintf("UPDATE `tab_kategorie` "
//            . "SET `parent`=%d,"
//            . "`category_name`='%s' WHERE `pk`=%d;"
//            , $kat->parent_id
//            , escapeString($kat->name)
//            , $kat->id
//        );
//
//        query($sql);
//        $kat = self::getKategorieByNameParent($kat->name, $kat->parent_id);
//        return $kat->id;
    }

    public static function getRezeptFromDatabase($sql):Rezept {
        $result = query($sql);
        if ($row = $result->fetch_assoc()) {
            $rezept = self::get($row);
            self::completeRezept($rezept);
            return $rezept;
        }
        return new Rezept();
    }

    private static function getListeRezepteFromDatabase(string $sql):array {
        $result = [];
        $mysqli = query($sql);
        while($row = $mysqli->fetch_assoc()){
            $rezept = self::get($row);
            self::completeRezept($rezept);
            $result[] = $rezept;
        }
        return $result;
    }

    private static function completeRezept(Rezept $rezept) {
        $rezept->tasks = [];
        $rezept->zutaten = [];
        $rezept->kategorien = Kategorie::getListeKategorieByRezeptId($rezept->id);
    }

    public static function getRezepteByUserid(int $userid):array {
        $sql = "select `pk`, `title`, `description`, `createdz`, `fs_benutzer` from `tab_rezepte`";
        if ($userid > 0) {
            $sql .= " where `fs_benutzer` = $userid";
        }
        $result = self::getListeRezepteFromDatabase($sql);
        foreach ($result as $rezept) {
            self::completeRezept($rezept);
        }
        return $result;
    }

    #[Pure]
    private static function get($row):Rezept {
        $result = new Rezept();
        $result->id = (int)$row['pk'];
        $result->title = $row['title'];
        $result->desc = $row['description'];
        $result->userid = (int)$row['fs_benutzer'];
        $result->create_dz = $row['createdz'];
        return $result;
    }

    #[ArrayShape(['id' => "int", 'userid' => "int", 'title' => "string", 'description' => "string", 'createdz' => "string", 'zubereitung' => "array", 'zutaten' => "array", 'kategorien' => "array"])]
    public function toArray(): array {
        return array (
            'id' => $this->id,
            'userid' => $this->userid,
            'title' => $this->title,
            'description' => $this->desc,
            'createdz' => $this->create_dz,
            'zubereitung' => $this->tasks,
            'zutaten' => $this->zutaten,
            'kategorien' => $this->kategorien,
        );
    }
}