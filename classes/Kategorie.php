<?php
declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Kategorie {
    public int $id = 0;
    public string $name = '';
    public int $parent_id = 0;

    public const SPLITTER = " --> ";

    /** @noinspection PhpUnused */
    public static function saveDefaultKategorien() {
        self::saveByName("Hauptgerichte".Kategorie::SPLITTER."Kartoffeln");
        self::saveByName("Hauptgerichte".Kategorie::SPLITTER."Nudeln");
        self::saveByName("Nachtisch");
        self::saveByName("Brot");
        self::saveByName("Brötchen");
        self::saveByName("Weihnachten");
        self::saveByName("Ostern");
    }

    public static function saveByName(string $category_name) {
        $arr = explode(Kategorie::SPLITTER, $category_name);
        $parent_id = 0;
        foreach ($arr as $name) {
            $kat = self::getKategorieByNameParent($name, $parent_id);
            if ($kat->id == 0) {
                // diese Kategorie gibt es noch nicht
                // --> anlegen
                $kat->parent_id = $parent_id;
                $kat->name = $name;
                $parent_id = self::save($kat);
                continue;
            }
            $parent_id = $kat->id;
        }
    }

    public static function save(Kategorie $kat):int {
        if ($kat->id > 0) {
            return self::update($kat);
        }
        return self::insert($kat);
    }

    private static function insert(Kategorie $kat):int {
        $sql = sprintf("INSERT INTO `tab_kategorie` "
            . "SET `parent`=%d,"
            . "`category_name`='%s';"
            , $kat->parent_id
            , escapeString($kat->name)
        );

        query($sql);
        $kat = self::getKategorieByNameParent($kat->name, $kat->parent_id);
        return $kat->id;
    }

    private static function update(Kategorie $kat):int {
        $sql = sprintf("UPDATE `tab_kategorie` "
            . "SET `parent`=%d,"
            . "`category_name`='%s' WHERE `pk`=%d;"
            , $kat->parent_id
            , escapeString($kat->name)
            , $kat->id
        );

        query($sql);
        $kat = self::getKategorieByNameParent($kat->name, $kat->parent_id);
        return $kat->id;
    }

    public static function getKategorieFromDatabase($sql):Kategorie {
        $result = query($sql);
        if ($row = $result->fetch_assoc()) {
            return Kategorie::get($row);
        }
        return new Kategorie();
    }

    private static function getListeKategorieFromDatabase(string $sql):array {
        $result = [];
        $mysqli = query($sql);
        while($row = $mysqli->fetch_assoc()){
            $result[] = Kategorie::get($row);
        }
        return $result;
    }

    public static function getKategorieById(int $id):Kategorie {
        $sql = sprintf("select `pk`, `parent`, `category_name` from `tab_kategorie` where pk = %d;"
            , $id
        );
        return self::getKategorieFromDatabase($sql);
    }

    public static function getKategorieByNameParent(string $name, int $parent_id):Kategorie {
        $sql = sprintf("select `pk`, `parent`, `category_name` from `tab_kategorie` where parent = %d and `category_name`='%s';"
            , $parent_id
            , escapeString($name)
        );
        return self::getKategorieFromDatabase($sql);
    }

    public static function saveKategorieForRezept(int $rezept_id, array $categories) {
        self::deleteKategorieByRezeptId($rezept_id);
        foreach ($categories as $category) {
            $sql = sprintf("INSERT INTO `tab_rezept_kategorie` "
                . "SET `fs_rezept`=%d,"
                . "`fs_kategorie`=%d;"
                , $rezept_id
                , $category->id
            );
            insert($sql);
        }
    }

    public static function getAlleKategorien():array {
        $sql = "select `pk`, `parent`, `category_name` from `tab_kategorie`;";
        $result = self::getListeKategorieFromDatabase($sql);

        foreach ($result as $kat) {
            $parents = self::getElternKategorien($kat->parent_id);
            $kat->name = $parents.$kat->name;
        }
        return $result;
    }

    public static function getElternKategorien(int $pk):string {
        if ($pk === 0)
            return "";

        $sql = sprintf("select `pk`, `parent`, `category_name` from `tab_kategorie` where pk = %d;",
            $pk
        );
        $kat = self::getKategorieFromDatabase($sql);

        return $kat->name . Kategorie::SPLITTER . self::getElternKategorien($kat->parent_id);
    }

    public static function getListeKategorieByRezeptId(int $rezept_id):array {
        $sql = sprintf("select kat.`pk`, `parent`, `category_name` from `tab_kategorie` kat join tab_rezept_kategorie rez_kat on (kat.pk = rez_kat.fs_kategorie) where rez_kat.fs_rezept = %d;",
            $rezept_id
        );
        return self::getListeKategorieFromDatabase($sql);
    }

    public static function testKategorieByRezeptId(int $rezept_id, int $category_id):bool {
        $sql = sprintf("select count(kat.`pk`) anz from `tab_kategorie` kat "
            . "join tab_rezept_kategorie rez_kat on (kat.pk = rez_kat.fs_kategorie) "
            . "where rez_kat.fs_rezept = %d and kat.`pk` = %d;"
            , $rezept_id
            , $category_id
        );
        $result=query($sql);
        if ($row = $result->fetch_assoc()) {
            return (int)$row['anz'] > 0;
        }
        return false;
    }

    public static function deleteKategorieByRezeptId(int $rezept_id) {
        $sql = sprintf("delete from `tab_rezept_kategorie` where fs_rezept = %d;",
            $rezept_id
        );
        query($sql);
    }

    #[Pure]
    private static function get($row):Kategorie {
        $result = new Kategorie();
        $result->id = (int)$row['pk'];
        $result->name = $row['category_name'];
        $result->parent_id = (int)$row['parent'];
        return $result;
    }

    /** @noinspection PhpUnused */
    #[ArrayShape(['id' => "int", 'name' => "string", 'parent' => "int"])]
    public function toArray(): array {
        return array (
            'id' => $this->id,
            'name' => $this->name,
            'parent' => $this->parent_id,
        );
    }
}