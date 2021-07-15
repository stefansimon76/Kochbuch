<?php
declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Zubereitung {
    public int $id = 0;
    public int $order = 1;
    public string $desc = "";
    public string $name = "";

    public static function deleteZubereitungByRezeptId(int $rezept_id) {
        $sql = sprintf("delete from `tab_rezept_zutaten` where fs_rezept = %d;",
            $rezept_id
        );
        query($sql);
    }

    public static function saveZubereitungForRezept(int $rezept_id, array $zubereitung) {
        self::deleteZubereitungByRezeptId($rezept_id);
        foreach ($zubereitung as $task) {
            $id = self::insert($task);
            $sql = sprintf("INSERT INTO `tab_rezept_zubereitung` "
                . "SET `fs_rezept`=%d,"
                . "`fs_zubereitung`=%d;"
                , $rezept_id
                , $id
            );
            insert($sql);
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

    /** @noinspection PhpUnused */
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