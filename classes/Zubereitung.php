<?php
declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Zubereitung {
    public int $id = 0;
    public int $order = 1;
    public string $desc = "";
    public string $name = "";

    public static function getZubereitungById(int $id): Zubereitung {
        if ($id > 0) {
            $sql = sprintf("SELECT * FROM `tab_zubereitung` where `pk`= %d;"
                , $id
            );
            return self::getZubereitungFromDatabase($sql);
        }
        return new Zubereitung();
    }

    public static function getZubereitungByRezeptId(int $rezept_id): array {
        if ($rezept_id > 0) {
            $sql = sprintf("SELECT * FROM `tab_zubereitung` join tab_rezept_zubereitung rez_zubereitung on (rez_zubereitung.fs_zubereitung = tab_zubereitung.pk) where rez_zubereitung.fs_rezept = %d;"
                , $rezept_id
            );
            return self::getListeZubereitungFromDatabase($sql);
        }
        return [];
    }

    public static function save(Zubereitung $zubereitung):int {
        if ($zubereitung->id > 0) {
            return self::update($zubereitung);
        }
        return self::insert($zubereitung);
    }

    public static function saveZubereitungForRezept(int $rezept_id, array $zubereitung) {
        $rezept_zubereitung = self::getListeZubereitungByRezeptId($rezept_id);
        var_dump($rezept_zubereitung);
        echo "<br>";
        var_dump($zubereitung);
        foreach ($zubereitung as $task) {
            self::insert($task);
        }
    }

    public static function getListeZubereitungByRezeptId(int $rezept_id):array {
        $sql = sprintf("select task.`pk`, `order`, `task_name`, `task_description` from `tab_zubereitung` task join tab_rezept_zubereitung rez_task on (task.pk = rez_task.fs_zubereitung) where rez_task.fs_rezept = %d;",
            $rezept_id
        );
        return self::getListeZubereitungFromDatabase($sql);
    }

    private static function insert(Zubereitung $task):int {
        $sql = sprintf("INSERT INTO `tab_zubereitung` "
            . "SET `order`=%d,"
            . "`task_name`='%s',"
            . "`task_description`='%s';"
            , $task->order > 0 ? $task->order : 1
            , escapeString($task->name)
            , escapeString($task->desc)
        );

        $task->id = insert($sql);
        return $task->id;
    }

    private static function update(Zubereitung $task):int {
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

    public static function getZubereitungFromDatabase($sql):Zubereitung {
        $result = query($sql);
        if ($row = $result->fetch_assoc()) {
            return self::get($row);
        }
        return new Zubereitung();
    }

    private static function getListeZubereitungFromDatabase(string $sql):array {
        $result = [];
        $mysqli = query($sql);
        while($row = $mysqli->fetch_assoc()){
            $result[] = self::get($row);
        }
        return $result;
    }

    #[Pure]
    private static function get($row):Zubereitung {
        $result = new Zubereitung();
        $result->id = (int)$row['pk'];
        $result->order = (int) $row['order'];
        $result->name = $row['task_name'];
        $result->desc = $row['task_description'];
        return $result;
    }

    #[ArrayShape(['id' => "int", 'order' => "int", 'name' => "string", 'desc' => "string"])]
    public function toArray(): array {
        return array (
            'id' => $this->id,
            'order' => $this->order,
            'name' => $this->name,
            'desc' => $this->desc
        );
    }
}