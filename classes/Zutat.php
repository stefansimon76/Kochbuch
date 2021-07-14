<?php
declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Zutat {
    public int $id = 0;
    public string $name = "";
    public float $menge = 0;
    public string $unit = "";

    public static function getZutatById(int $id): Zutat {
        if ($id > 0) {
            $sql = sprintf("SELECT * FROM `tab_zutaten` where `pk`= %d;"
                , $id
            );
            return self::getZutatFromDatabase($sql);
        }
        return new Zutat();
    }

    public static function getZutatenByRezeptId(int $rezept_id): array {
        if ($rezept_id > 0) {
            $sql = sprintf("SELECT * FROM `tab_zutaten` join tab_rezept_zutaten rez_zutaten on (rez_zutaten.fs_zutaten = tab_zutaten.pk) where rez_zutaten.fs_rezept = %d;"
                , $rezept_id
            );
            return self::getListeZutatenFromDatabase($sql);
        }
        return [];
    }

    public static function save(Zutat $zutat):int {
        if ($zutat->id > 0) {
            return self::update($zutat);
        }
        return self::insert($zutat);
    }

    public static function saveZutatenForRezept(int $rezept_id, array $zutaten) {
        $rezept_zutaten = self::getListeZutatenByRezeptId($rezept_id);
        foreach ($zutaten as $zutat) {
            self::insert($zutat);
            $sql = sprintf("INSERT INTO `tab_rezept_zutaten` "
                . "SET `fs_rezept`=%d,"
                . "`fs_zutaten`=%d;"
                , $rezept_id
                , $zutat->id
            );
            insert($sql);
        }
    }

    public static function getListeZutatenByRezeptId(int $rezept_id):array {
        $sql = sprintf("select zutat.`pk`, `menge`, `einheit`, `zutat_name` from `tab_zutaten` zutat join tab_rezept_zutaten rez_zutat on (zutat.pk = rez_zutat.fs_zutaten) where rez_zutat.fs_rezept = %d;",
            $rezept_id
        );
        return self::getListeZutatenFromDatabase($sql);
    }

    private static function insert(Zutat $zutat):int {
        $sql = sprintf("INSERT INTO `tab_zutaten` "
            . "SET `menge`=%f,"
            . "`einheit`='%s',"
            . "`zutat_name`='%s';"
            , $zutat->menge
            , $zutat->unit
            , escapeString($zutat->name)
        );

        $zutat->id = insert($sql);
        return $zutat->id;
    }

    private static function update(Zutat $zutat):int {
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

    public static function getZutatFromDatabase($sql):Zutat {
        $result = query($sql);
        if ($row = $result->fetch_assoc()) {
            return self::get($row);
        }
        return new Zutat();
    }

    private static function getListeZutatenFromDatabase(string $sql):array {
        $result = [];
        $mysqli = query($sql);
        while($row = $mysqli->fetch_assoc()){
            $result[] = self::get($row);
        }
        return $result;
    }

    #[Pure]
    private static function get($row):Zutat {
        $result = new Zutat();
        $result->id = (int)$row['pk'];
        $result->menge = floatval($row['menge']);
        $result->unit = $row['einheit'];
        $result->name = $row['zutat_name'];
        return $result;
    }

    #[ArrayShape(['id' => "int", 'menge' => "string", 'unit' => "string", 'name' => "int"])]
    public function toArray(): array {
        return array (
            'id' => $this->id,
            'menge' => $this->menge,
            'unit' => $this->unit,
            'name' => $this->name
        );
    }
}