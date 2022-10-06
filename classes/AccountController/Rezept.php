<?php
declare(strict_types=1);


class AccountController_Rezept extends AccountController_Base
{
    // ####################################################
    // GET Liste Rezepte
    public static function renderListRezepte(array $rezepte=[]) {
        $userid = getCurrentUserID();
        $data=[
            'editable'=> $userid > 0,
            'rezepte' => $rezepte,
        ];
        $data = array_merge($data, $_SESSION["userdata"]);
        Layout::setBodyRenderer(RENDER_BODY_LISTE_REZEPTE);
        echo Layout::getInstance()->render('index', $data);
    }

    // ####################################################
    // zufälliges Rezept anzeigen
    public static function getRezeptById(int $rezept_id):array {
        $user_id = 0;
        if ($rezept_id == 0) {
            if (isset($_SESSION['userid'])) {
                $user_id = (int)$_SESSION['userid'];
            }
            $rezept_id = Rezept::getRandomRezeptIdByUserid($user_id);
        }
        $rezept = Rezept::getRezeptById($rezept_id);
        $zutaten = [];
        foreach ($rezept->zutaten as $zutat) {
            $zutaten[] = ["menge" => $zutat->menge == '0' ? '' : $zutat->menge, "unit" => $zutat->unit, "name"=>$zutat->name];
        }
        $zubereitung = [];
        foreach ($rezept->tasks as $task) {
            //$desc_value = preg_replace('/\n/', '<br/>', $task->desc);
            $lines = preg_split('/\n/', $task->desc);
            $desc_value = [];
            foreach ($lines as $index => $line) {
                $desc_value[$index] = $line;
            }
            $zubereitung[] = ["name" => $task->name, "desc" => $desc_value];
//            echo print_r($zubereitung); exit();
//            $zubereitung[] = ["name" => $task->name, "desc" => $task->desc];
        }
        $data=[
            'rezept_id' => $rezept_id,
            'rezept' => self::getRezeptForFrontend($rezept),
            'zutaten' => $zutaten,
            'zubereitung' => $zubereitung,
            'editable' => $rezept->userid == $user_id,
        ];
        return array_merge($data, $_SESSION["userdata"]);
    }

    // ####################################################
    // GET einzelnes Rezept by ID
    public static function renderRezeptById(int $rezept_id) {
        $data = self::getRezeptById($rezept_id);
        Layout::setBodyRenderer(RENDER_BODY_SINGLE_REZEPT);
        echo Layout::getInstance()->render('index', $data);
    }

    private static function getRezeptForFrontend(Rezept $rezept):array {
        $img_name = self::getImageName($rezept);
        $img_width = 150;
        $img_height = 120;
        $userid = getCurrentUserID();
        return [
            "editable" => $rezept->userid == $userid,
            "img_name" => $img_name,
            "img_width" => $img_width,
            "img_height" => $img_height,
            "rezept_id" => $rezept->id,
            "rezept_title" => $rezept->title,
            "rezept_desc" => $rezept->desc,
        ];
    }

    private static function getRezepteForFrontend(array $rezepte):array {
        $result = [];
        foreach ($rezepte as $rezept) {
            $result[] = self::getRezeptForFrontend($rezept);
        }
        return $result;
    }

    private static function getImageName(Rezept $rezept):string {
        $storagePath = STORAGE_DIR.'/pictures/'; // pfad auf Server
        $picturePath = '/storage/pictures/'; // url
        $img_name = "/rezept_".$rezept->id.".jpg";
        if (is_file($storagePath.$img_name))
            // Wenn Datei auf Server vorhanden, URL zurückgeben
            return $picturePath.$img_name;

        $img_name = "/rezept_".$rezept->id.".png";
        if (is_file($storagePath.$img_name))
            // Wenn Datei auf Server vorhanden, URL zurückgeben
            return $picturePath.$img_name;

        // Wenn Datei auf Server vorhanden URL zurückgeben
        // leeres (transparentes) Image zurüchgeben
        return $picturePath . "blank.png";
    }

    public static function renderEditRezept(string $str_rezept_id = "0", array $errors = []) {
        $rezept_id = (int) $str_rezept_id;
        $errors = [];
        $pre_categories = [];
        $pre_zutaten = [];
        $pre_zubereitung = [];
        $rezept = new Rezept();
        if ($rezept_id > 0) {
            $rezept = Rezept::getRezeptById($rezept_id);
            if ($rezept->userid !== getCurrentUserID()) {
                redirect("/rezepte/".$str_rezept_id);
            }
        }

        $kategorien = Kategorie::getAlleKategorien();
        foreach ($kategorien as $index => $kat) {
            $pre_categories[$index]["id"] = "pk".$kat->id;
            $pre_categories[$index]["name"] = $kat->name;
            $pre_categories[$index]["checked"] = Kategorie::testKategorieByRezeptId($rezept_id, $kat->id) ? "checked" : "";
        }

        $zutaten = Zutat::getListeZutatenByRezeptId($rezept_id);
        foreach ($zutaten as $index => $zutat) {
            $pre_zutaten[$index]["index"] = $index + 1;
            $pre_zutaten[$index]["menge"] = $zutat->menge == '0' ? '' : $zutat->menge;
            $pre_zutaten[$index]["unit"] = $zutat->unit;
            $pre_zutaten[$index]["name"] = $zutat->name;
        }

        $tasks = Zubereitung::getListeZubereitungByRezeptId($rezept_id);
        foreach ($tasks as $index => $task) {
            $pre_zubereitung[$index]["index"] = $index + 1;
            $pre_zubereitung[$index]["name"] = $task->name;
            $pre_zubereitung[$index]["desc"] = $task->desc;
        }

        $data=[
            'errors' => $errors,
            'categories' =>$pre_categories,
            'zutaten' =>$pre_zutaten,
            'zubereitung' =>$pre_zubereitung,
            'rezept_id' => $rezept_id,
            'title' => $rezept->title,
            'description' => $rezept->desc,
            'visibility_public' => $rezept->unlock_dz != "" ? "checked" : "",
            'visibility_private' => $rezept->unlock_dz == "" ? "checked" : "",
        ];
        $data = array_merge($data, $_SESSION["userdata"]);
        Layout::setBodyRenderer(RENDER_BODY_EDIT_REZEPT);
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

    public static function deleteRezept(int $rezept_id) {
        Rezept::delete($rezept_id);
        self::renderRezepteByUserId(getCurrentUserID());
    }

    // ####################################################
    // POST searchRezept
    public static function searchRezept() {
        $suchtext = filter_input(INPUT_POST, 'suchtext', FILTER_SANITIZE_STRING);
        //echo $suchtext;
        $arrRezepte=Rezept::findRezeptBySuchtext($suchtext);
        $rezepte = self::getRezepteForFrontend($arrRezepte);
        self::renderListRezepte($rezepte);
    }

    // ####################################################
    // POST saveRezept
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
        $unlockdz = '';
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

                $zutat = new Zutat();
                $zutat->menge = floatval($menge);
                $zutat->unit = $unit;
                $zutat->name = $name;
                $zutaten[] = $zutat;
            }

            if( str_starts_with($key, 'taskname_')) {
                $id=substr($key, strlen('taskname_'));
                $taskname = filter_input(INPUT_POST, "taskname_$id", FILTER_SANITIZE_STRING);
                $taskdesc = filter_input(INPUT_POST, "taskdesc_$id", FILTER_SANITIZE_STRING);
                // Taskname muss vorhanden sein, sonst skip und weiter
                if (empty($taskname))
                    continue;

                $task = new Zubereitung();
                $task->name = $taskname;
                $task->desc = $taskdesc;
                $tasks[]=$task;
            }

            if (str_starts_with($key, "category_")) {
                if ($value == "true") {
                    $id = (int) substr($key, strlen('category_pk'));
                    $categories[] = Kategorie::getKategorieById($id);
                }
            }

            if ($key == "visibility") {
                if ($value == "visible") {
                    $unlockdz = date('Y-m-d H:i:s.0');
                } else {
                    $unlockdz = '';
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
        if ($unlockdz == '') {
            $rezept->unlock_dz = '';
        } elseif ($rezept->unlock_dz == '') {
            $rezept->unlock_dz = $unlockdz;
        }


        if (count($errors) == 0) {
            $rezept_id = strval(Rezept::save($rezept));
            if (isset($mainImage)) {
                if (!uploadPicture('rezept_'.$rezept_id, $mainImage)) {
                    $errors[] = "Fehler beim Bilder-Upload";
                    self::renderEditRezept(strval($rezept_id), $errors);
                }
            }
            redirect("rezepte/" . $rezept_id);
        }
        self::renderEditRezept(strval($rezept_id), $errors);
    }
}

// todo: Rezepte nach Schlüsselwörtern durchsuchen
// todo: Sortieren/Filtern nach Kategorie