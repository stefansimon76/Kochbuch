<?php
declare(strict_types=1);


class AccountController_Rezept extends AccountController_Base
{
    // ####################################################
    // GET Rezepte-Seite
    public static function renderListRezepte(array $errors=[]) {
        $data=[
            'errors' => $errors,
        ];
        $data = array_merge($data, $_SESSION["userdata"]);
        Layout::setBodyRenderer(RENDER_BODY_LISTE_REZEPTE);
        echo Layout::getInstance()->render('index', $data);
    }

    public static function renderCreateRezept(array $errors=[]) {
        $kategorien = Kategorie::getAlleKategorien();
        $categories = [];
        foreach ($kategorien as $index => $kat) {
            $categories[$index]["id"] = "pk".$kat->id;
            $categories[$index]["name"] = $kat->name;
        }
        $data=[
            'errors' => $errors,
            'categories' =>$categories,
            'rezept_id' => "0"
        ];
        $data = array_merge($data, $_SESSION["userdata"]);
        Layout::setBodyRenderer(RENDER_BODY_CREATE_REZEPT);
        echo Layout::getInstance()->render('index', $data);
    }

    public static function renderRezepteByLoginname(string $loginname) {
        if ($_SESSION['userdata']['loginname'] === $loginname) {
            self::renderRezepteByUserId(getCurrentUserID());
        } else {
            self::renderRezepteByUserId(0);
        }
    }

    public static function renderRezepteByUserId(int $userid) {
        self::renderListRezepte([]);
    }

    // ####################################################
    // POST addNewRezept
    public static function saveRezept() {
        $allowedTypes = ['image/jpeg','image/png'];

        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $pictures = normalizeFile($_FILES['image']);
        $errors=[];
        $zutaten=[];
        $tasks=[];
        $categories = [];
        $mainImage = null;
        if (count($pictures) > 0) {
            foreach ($pictures as $pic) {
                if (!in_array($pic['type'], $allowedTypes)) {
                    $errors[] = "nicht unterstützter Dateityp: ". $pic['type'];
                }
            }
            $mainImage = $pictures[0];
        }

        $rezept_id = 0;
        foreach($_POST as $key => $value)
        {
            if ($key === "rezept_id") {
                $rezept_id = (int)$value;
                continue;
            }

            if( str_starts_with($key, 'menge_')) {
                $id=substr($key, strlen('menge_'));
                $menge = filter_input(INPUT_POST, "menge_$id", FILTER_VALIDATE_FLOAT);
                $unit = filter_input(INPUT_POST, "unit_$id", FILTER_SANITIZE_STRING);
                $name = filter_input(INPUT_POST, "name_$id", FILTER_SANITIZE_STRING);
                // Menge und Maßeinheit dürfen fehlen, aber
                // Zeile ignorieren, wenn $name leer ist
                // z.B.: bei "etwas rote Lebensmittelfarbe" braucht man keine
                // Mengenangabe oder Maßeinheit
                if (empty($name))
                    continue;

                // todo: Speichern
                $zutaten[] = ["menge"=>floatval($menge), "unit"=>$unit, "name"=>$name];
            }

            if( str_starts_with($key, 'taskname_')) {
                $id=substr($key, strlen('taskname_'));
                $taskname = filter_input(INPUT_POST, "taskname_$id", FILTER_SANITIZE_STRING);
                $taskdesc = filter_input(INPUT_POST, "taskdesc_$id", FILTER_SANITIZE_STRING);
                // Menge und Maßeinheit dürfen fehlen, aber
                // Zeile ignorieren, wenn $name leer ist
                // z.B.: bei "etwas rote Lebensmittelfarbe" braucht man keine
                // Mengenangabe oder Maßeinheit
                if (empty($taskname))
                    continue;

                $pictures = normalizeFile($_FILES["taskimg_$id"]);
                $taskImage = null;
                if (count($pictures) > 0) {
                    foreach ($pictures as $pic) {
                        if (!in_array($pic['type'], $allowedTypes)) {
                            $errors[] = "nicht unterstützter Dateityp: ". $pic['type'];
                        }
                    }
                    $taskImage = $pictures[0];
                }
                $tasks=["name" => $taskname, "desc" => $taskdesc, "image" => $taskImage];
            }

            if (str_starts_with($key, "category_")) {
                if ($value == "true") {
                    $id = (int) substr($key, strlen('category_pk'));
                    $categories[] = Kategorie::getKategorieById($id);
                }
            }
        }

        $rezept = Rezept::getRezeptById($rezept_id);
        $rezept->kategorien = $categories;
        $rezept->title=$title;
        $rezept->desc = $description;
        $rezept->userid = getCurrentUserID();
        $rezept->zutaten = $zutaten;
        $rezept->tasks = $tasks;

        if (count($errors) == 0) {
            if (isset($mainImage)) {
                $subdir = "";
                // todo: Rezept speichern und id als subdir setzen
                // todo: Bild in Datenbank ablegen
                if (!uploadPicture($subdir, $mainImage)) {
                    $errors[] = "Fehler beim Bilder-Upload";
                    self::renderCreateRezept($errors);
                }
            }
        }
        self::renderListRezepte($errors);
    }
}