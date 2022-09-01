<?php
declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Zutat {
    public int $id = 0;
    public string $name = "";
    public float $menge = 0;
    public string $unit = "";

    public static function saveZutatenForRezept(int $rezept_id, array $zutaten): void {
        self::deleteZutatenByRezeptId($rezept_id);
        foreach ($zutaten as $zutat) {
            $id = self::insert($zutat);
            $sql = sprintf("INSERT INTO `tab_rezept_zutaten` "
                . "SET `fs_rezept`=%d,"
                . "`fs_zutaten`=%d;"
                , $rezept_id
                , $id
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

    public static function deleteZutatenByRezeptId(int $rezept_id): void {
        $sql = sprintf("delete from `tab_rezept_zutaten` where fs_rezept = %d;",
            $rezept_id
        );
        query($sql);
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

    /** @noinspection PhpUnused */
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