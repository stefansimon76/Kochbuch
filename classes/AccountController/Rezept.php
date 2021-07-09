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
                $menge = filter_input(INPUT_POST, "menge_$id", FILTER_SANITIZE_STRING);
                $unit = filter_input(INPUT_POST, "unit_$id", FILTER_SANITIZE_STRING);
                $name = filter_input(INPUT_POST, "name_$id", FILTER_SANITIZE_STRING);
                // Menge und Maßeinheit dürfen fehlen, aber
                // Zeile ignorieren, wenn $name leer ist
                // z.B.: bei "etwas rote Lebensmittelfarbe" braucht man keine
                // Mengenangabe oder Maßeinheit
                if (empty($name))
                    continue;

                // todo: Speichern
                $errors[] = "id:$id";
                $errors[] = "menge:$menge";
                $errors[] = "unit:$unit";
                $errors[] = "name:$name";
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

                // todo: Speichern
                $errors[] = "taskname:$taskname";
                $errors[] = "taskdesc:$taskdesc";
                $errors[] = "image:".$taskImage['tmp_name'];
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
//
//        if ($user->pk > 0) {
//            $errors[] = 'Der Anmeldename ist bereits vergeben';
//        }
//
//        if (strlen($loginname) < 5) {
//            $errors[] = "Der Anmeldename muss mindestens 5 Zeichen lang sein";
//        }
//
//        if (!preg_match('/^[a-zA-Z0-9]+$/', $loginname))
//        {
//            $errors[] = "Der Anmeldename darf nur Buchstaben und Ziffern enthalten (keine Sonderzeichen und kein Leerzeichen)";
//        }
//
//        if ($pw1 !== $pw2) {
//            $errors[] = 'Die Passwörter stimmen nicht überein';
//        }
//
//        if (!$email) {
//            $errors[] = 'Bitte geben Sie eine korrekte E-Mail Adresse ein';
//        }
//
//        if ($terms !== 'on') {
//            $errors[] = 'Bitte akzeptieren Sie die Nutzungsbedingungen';
//        }
//
//        $user = Benutzer::findByMail($email);
//        if ($user->pk > 0) {
//            $errors[] = 'Die E-Mail Adresse ist bereits einem anderen Benutzer zugeordnet, bitte wählen Sie eine andere.';
//        }
//
        if (count($errors) == 0) {
            if (isset($mainImage)) {
                $subdir = ""; // todo: Rezept speichern und id als subdir setzen
                // todo: Bild in Datenbank ablegen
                if (!uploadPicture($subdir, $mainImage)) {
                    $errors[] = "Fehler beim Bilder-Upload";
                    self::renderCreateRezept($errors);
                }
            }
            self::renderCreateRezept([]);
//            $user = Benutzer::createAccount($realname, $loginname, $pw1, $email);
//            if ($user->pk > 0) {
//                $_SESSION["realname"] = $realname;
//                $_SESSION["loginname"] = $loginname;
//                $user->sendActivationMail();
//                self::renderThankYou();
//            }
        } else {
            self::renderListRezepte($errors);
        }
    }
}