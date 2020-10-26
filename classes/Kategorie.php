<?php
declare(strict_types=1);

class Kategorie {
    public $id = 0;
    public $name = '';
    public $subkat_id = 0;

    public static function getListeKategorie() {
        $result = null;
        $db = (new DB())->getConnection();
        if ($stmt = $db->query('SELECT `pk_kategorie`, `sub_kategorie`, `name` FROM `KATEGORIE`')) {
            while($row = $stmt->fetch_assoc()){
                $result[] = Kategorie::get($row);
            }
        
            $stmt->close();
        } else {
            print("Keine Kategorien gefunden");
        }
        return $result;
    }

    private static function get($row) {
        $result = new Kategorie();
        $result->id = $row['pk_kategorie'];
        $result->name = $row['name'];
        $result->subkat_id = $row['sub_kategorie'];
        return $result;
    }

    public function toArray() {
        return array (
            'id' => $this->id,
            'name' => $this->name,
            'subkat' => $this->subkat_id,
        );
    }
}