<?php
declare(strict_types=1);


class AccountController_Rezept extends AccountController_Base
{
    // ####################################################
    // GET Rezepte-Seite
    public static function renderListRezepte(array $rezepte=[]) {
        $data=[
            'rezepte' => $rezepte,
        ];
        $data = array_merge($data, $_SESSION["userdata"]);
        Layout::setBodyRenderer(RENDER_BODY_LISTE_REZEPTE);
        echo Layout::getInstance()->render('index', $data);
    }

    private static function getRezepteForFrontend(array $rezepte):array {
        $result = [];
        foreach ($rezepte as $rezept) {
            $img_name = self::getImageName($rezept);
            $img_width = 150;
            $img_height = 120;
            $result[] = [
                "img_name" => $img_name,
                "img_width" => $img_width,
                "img_height" => $img_height,
                "rezept_id" => $rezept->id,
                "rezept_title" => $rezept->title,
                "rezept_desc" => $rezept->desc
            ];
        }
        return $result;
    }

    private static function getImageName(Rezept $rezept):string {
        $storagePath = STORAGE_DIR.'/pictures/'; // pfad auf Server
        $picturePath = '/storage/pictures/'; // url
        $img_name = $rezept->id ."/".$rezept->id.".jpg";
        if (is_file($storagePath.$img_name))
            // Wenn Datei auf Server vorhanden, URL zurückgeben
            return $picturePath.$img_name;

        $img_name = $rezept->id ."/".$rezept->id.".png";
        if (is_file($storagePath.$img_name))
            // Wenn Datei auf Server vorhanden, URL zurückgeben
            return $picturePath.$img_name;

        // Wenn Datei auf Server vorhanden, URL zurückgeben
        // leeres (transparentes) Image zurüchgeben
        return $picturePath . "blank.png";
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
            self::renderRezepte();
        }
    }

    public static function renderRezepte() {
        self::renderRezepteByUserId(0);
    }

    public static function renderRezepteByUserId(int $userid) {
        $rezepte = Rezept::getRezepteByUserid($userid);
        $rezepte = self::getRezepteForFrontend($rezepte);
        self::renderListRezepte($rezepte);
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
            $subdir = strval(Rezept::save($rezept));
            if (isset($mainImage)) {
                if (!uploadPicture($subdir, $mainImage)) {
                    $errors[] = "Fehler beim Bilder-Upload";
                    self::renderCreateRezept($errors);
                }
            }
            redirect("Rezepte");
        }
        self::renderCreateRezept($errors);
    }
}
// todo: Bekannter Bug: wenn man im Browser zurück geht kann man dasselbe Rezept mehrfach speichern
// todo: einzelnes Rezept anzeigen und bearbeiten/löschen