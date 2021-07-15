function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function isPositiveNumber(n) {
    return isNumber(n) && n>0;
}

function getRandomId() {
    return Math.random().toString(36).slice(2);
}

function cookieOk() {
    let now = new Date(); // Variable für aktuelles Datum
    let lifetime = now.getTime(); // Variable für Millisekunden seit 1970 bis aktuelles Datum
    let deleteCookie = lifetime + 2592000000; // Macht den Cookie 30 Tage gültig.

    now.setTime(deleteCookie);
    let enddate = now.toUTCString();

    document.cookie = "setCookieHinweis = set; path=/; secure:false; expires=" + enddate;
    document.getElementById("cookie-popup").classList.add("hidden");
}

function addZutat() {
    let liste = document.getElementById("lstZutaten");
    liste.appendChild(createEmptyZutatRow(liste.children.length + 1));
}

function createEmptyZutatRow(id) {
    let row = createBorder()
    row.id = "zutat_" + id

    let content = createDiv("row")
    content.appendChild(createZutatColumnMenge(row.id))
    content.appendChild(createZutatColumnEinheit(row.id))
    content.appendChild(createZutatColumnName(row.id))
    content.appendChild(createRemoveButton("lstZutaten", row.id))

    row.appendChild(content)
    return row
}

function createZutatColumnMenge($rowId) {
    // Spalte div erstellen
    let columnDiv = createDiv("col-6 col-md-2 pr-0");

    // input-field erstellen
    let input = createTextInput("Menge", 5);

    // label für das Input-Field konfigurieren
    let label = createHiddenLabel();
    input.id = getRandomId();
    input.name = "menge_" + $rowId;
    label.htmlFor = input.id;

    // Label + Input-Field dem div hinzufügen
    columnDiv.appendChild(label);
    columnDiv.appendChild(input);

    return columnDiv;
}
function createZutatColumnEinheit($rowId) {
    // Spalte div erstellen
    let columnDiv = createDiv("col-6 col-md-2");

    // input-field erstellen
    let input = createTextInput("Einheit", 10);

    // label für das Input-Field konfigurieren
    let label = createHiddenLabel();
    input.id = getRandomId();
    input.name = "unit_" + $rowId;
    label.htmlFor = input.id;

    // Label + Input-Field dem div hinzufügen
    columnDiv.appendChild(label);
    columnDiv.appendChild(input);

    return columnDiv;
}
function createZutatColumnName($rowId) {
    // Spalte div erstellen
    let columnDiv = createDiv("col-9 col-md-6 pr-0");

    // input-field erstellen
    let input = createTextInput("Zutatenname", 100);

    // label für das Input-Field konfigurieren
    let label = createHiddenLabel();
    input.id = getRandomId();
    input.name = "name_" + $rowId;
    label.htmlFor = input.id;

    // Label + Input-Field dem div hinzufügen
    columnDiv.appendChild(label);
    columnDiv.appendChild(input);

    return columnDiv;
}
function createRemoveButton($parentControl, $rowId) {
    // Spalte div erstellen
    let columnDiv = createDiv("col-3 col-md-2 text-right");

    // Button-Div erstellen
    let buttonDiv = createDiv("btn alert-danger");
    buttonDiv.innerHTML = "X";
    buttonDiv.onclick = function () {
        let liste = document.getElementById($parentControl);
        liste.removeChild(document.getElementById($rowId));
        for (let i = 0; i < liste.children.length; i++) {
            let child = liste.children[i];
            let sub = child.querySelectorAll("input,textarea")
            if (child.id.startsWith("zutat")) {
                let id = "zutat_" + (i + 1);
                for (let k = 0; k < sub.length; k++) {
                    let zutat = sub[k];
                    if (zutat.getAttribute("name").startsWith("menge")) {
                        zutat.setAttribute("name", "menge_" + id)
                    }
                    if (zutat.getAttribute("name").startsWith("unit")) {
                        zutat.setAttribute("name", "unit_" + id)
                    }
                    if (zutat.getAttribute("name").startsWith("name")) {
                        zutat.setAttribute("name", "name_" + id)
                    }
                }
            }
            if (child.id.startsWith("task")) {
                let id = "task_" + (i + 1);
                for (let k = 0; k < sub.length; k++) {
                    let task = sub[k];
                    if (task.getAttribute("name").startsWith("taskname")) {
                        task.setAttribute("name", "taskname_" + id)
                    }
                    if (task.getAttribute("name").startsWith("taskdesc")) {
                        task.setAttribute("name", "taskdesc_" + id)
                    }
                    if (task.getAttribute("name").startsWith("taskimg")) {
                        task.setAttribute("name", "taskimg_" + id)
                    }
                }
            }
        }
    };

    columnDiv.appendChild(buttonDiv);

    return columnDiv;
}

function addTask() {
    let liste = document.getElementById("lstTasks");
    liste.appendChild(createEmptyTaskRow(liste.children.length + 1));
}

function createEmptyTaskRow(id) {
    let row = createBorder()
    row.id = "task_" + id

    //let content = createDiv("row")
    row.appendChild(createTaskHeader(row.id))
    row.appendChild(createTaskDescription(row.id))
    row.appendChild(createTaskPicture(row.id));

    //row.appendChild(content)
    return row
}

function createTaskHeader($rowId) {
    let column1 = createDiv("col-9 col-md-10 pr-0")

    let input = createTextInput("Vorbereitung, Zubereitung, Anweisungen", 2000)
    input.id = getRandomId()
    input.name = "taskname_" + $rowId;

    let label = createHiddenLabel()
    label.htmlFor = input.id

    column1.appendChild(label)
    column1.appendChild(input)



    let row = createDiv("row")
    row.appendChild(column1)
    row.appendChild(createRemoveButton("lstTasks", $rowId))

    return row;
}

function createTaskDescription($rowId) {
    // Spalte div erstellen
    let divDesc = createDiv("");

    // input-field erstellen
    let input = createTextAreaInput("Arbeitsschritt beschreiben", 2000);
    input.name = "taskdesc_" + $rowId;

    // label für das Input-Field konfigurieren
    let label = createHiddenLabel();
    input.id = getRandomId();
    label.htmlFor = input.id;

    // Label + Input-Field dem div hinzufügen
    divDesc.appendChild(label);
    divDesc.appendChild(input);

    return divDesc;
}

function createTaskPicture($rowId) {
    let divRow = createDiv("row");
    let divCol1 = createDiv("col-md-3");
    divCol1.innerHTML = "Fügen Sie dem Arbeitsschritt ein Bild hinzu (jpg/png)";
    let divCol2 = createDiv("col my-auto");
    let inputFile = createInput("file", "");
    inputFile.name = "taskimg_" + $rowId + "[]";
    divCol2.appendChild(inputFile)

    divRow.appendChild(divCol1);
    divRow.appendChild(divCol2);
    return divRow;
}

function createDiv($className) {
    let element = document.createElement("div");
    element.className = $className;
    return element;
}

function createBorder() {
    return createDiv("border rounded mb-1 bg-light")
}

function createHiddenLabel() {
    let element = document.createElement("label")
    element.hidden = true
    return element
}

function createTextInput($placeholder, $maxLength) {
    let element = createInput("text", "form-control")
    element.placeholder = $placeholder
    element.maxLength = $maxLength
    return element
}

function createTextAreaInput($placeholder, $maxLength) {
    let element = document.createElement("textarea")
    element.className = "form-control"
    element.placeholder = $placeholder
    element.maxLength = $maxLength
    element.rows = 3
    return element
}

function createInput($type, $classname) {
    let element = document.createElement("input")
    element.className = $classname;
    element.type = $type
    return element
}

function validateRegisterForm() {
    removeErrors();
    let hasErrors = 0;
    if (this.loginname.value.length < 5) {
        hasErrors++;
        insertError("Der Anmeldename muss mindestens 5 Zeichen lang sein");
    }

    let loginnameRegex = /^[a-zA-Z0-9]+$/;
    if (!this.loginname.value.match(loginnameRegex)) {
        insertError("Der Anmeldename darf nur Buchstaben und Ziffern enthalten (keine Sonderzeichen und kein Leerzeichen)");
    }

    if (this.password.value.length < 8) {
        hasErrors++;
        insertError("Das Passwort muss mindestens 8 Zeichen lang sein");
    }
    if (this.password.value !== this.passwordRepeat.value) {
        hasErrors++;
        insertError("Die Passwörter müssen identisch sein, um Tippfehler ausschließen zu können");
    }
    if (this.email.value.length < 5) {
        hasErrors++;
        insertError("Bitte geben Sie eine gültige E-Mail Adresse ein");
    }

    let terms = document.getElementById('terms');
    if (!terms.checked) {
        hasErrors++;
        insertError("Bitte akzeptieren Sie die Nutzungsbedingungen");
    }

    if (hasErrors > 0)
        return false;

    // Password hashen und nur den Hash posten
    this.md5pw1.value = md5(this.password.value);
    this.password.value = '';

    this.md5pw2.value = md5(this.passwordRepeat.value);
    this.passwordRepeat.value = '';
    return true;
}

function validateLoginForm() {
    removeErrors();
    let hasErrors = 0;
    if (this.loginname.value.length < 5) {
        hasErrors++;
        insertError("Der Anmeldename muss mindestens 5 Zeichen lang sein");
    }

    if (this.password.value.length < 8) {
        hasErrors++;
        insertError("Das Passwort muss mindestens 8 Zeichen lang sein");
    }

    if (hasErrors > 0)
        return false;

    // Password hashen und nur den Hash posten
    this.md5pw1.value = md5(this.password.value);
    this.password.value = '';
    return true;
}

function validatePwChangeForm() {
    removeErrors();
    let hasErrors = 0;
    if (this.password.value.length < 8) {
        hasErrors++;
        insertError("Das Passwort muss mindestens 8 Zeichen lang sein");
    }
    if (this.password.value !== this.passwordRepeat.value) {
        hasErrors++;
        insertError("Die Passwörter müssen identisch sein, um Tippfehler ausschließen zu können");
    }
    if (hasErrors > 0)
        return false;

    // Password hashen und nur den Hash posten
    this.md5pwold.value = md5(this.password_old.value);
    this.password_old.value = '';

    this.md5pw1.value = md5(this.password.value);
    this.password.value = '';

    this.md5pw2.value = md5(this.passwordRepeat.value);
    this.passwordRepeat.value = '';
    return true;
}

function validateRezeptForm() {
    removeErrors();
    let hasErrors = 0;
    if (this.title.value.length < 10) {
        hasErrors++;
        insertError("Der Titel muss zwischen 10 und 100 Zeichen lang sein");
    }
    if (this.description.value < 10) {
        hasErrors++;
        insertError("Die Beschreibung muss zwischen 10 und 2000 Zeichen lang sein");
    }

    if (validateMengeInput()) {
        hasErrors++;
        insertError("Die Menge muss ein numerischer Wert sein");
    }

    if (hasErrors > 0)
        return false;

    prepareCategories();
    return true;
}

function validateMengeInput() {
    let categories = document.querySelectorAll('*[name^="menge"]');
    categories.forEach(element => {
        // Menge ist optional
        if (element.value !== "") {
            // aber wenn die Menge angegeben wurde,
            // muss es eine gültige Zahl sein
            // also "," mit "." ersezuen
            element.value = element.value.replace(/,/g, '.')
            // und prüfen, ob es sich um einen numerischen Wert handelt
            // z.B.
            // "     |    | etwas Salz" (ohne Menge)     => OK
            // "1,5  | TL | Salz " -> "1.5 | TL | Salz " => OK
            // "etwas|    | Salz"                        => Fehler
            if (!isPositiveNumber(element.value)) {
                insertError("'" + element.value + "' ist kein numerischer Wert");
                return true;
            }
        }
    });
    return false;
}

function prepareCategories() {
    let categories = document.querySelectorAll('*[id^="pk"]');
    categories.forEach(element => {
        let elem = document.getElementById("category_" + element.id);
        elem.value=element.checked;
    });
}

function insertError($message) {
    let tag = document.createElement("p");
    //tag.className="mb-0";
    let text = document.createTextNode($message);
    tag.appendChild(text);
    let element = document.getElementById("client_errors");
    element.appendChild(tag);
    element.hidden = false;
}

function removeErrors() {
    let element = document.getElementById("client_errors");
    element.hidden = true;
    element.innerHTML = '';
}