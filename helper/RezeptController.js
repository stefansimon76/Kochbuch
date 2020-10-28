function getRandomId() {
    return Math.random().toString(36).slice(2);
}

function addZutat() {
    let liste = document.getElementById("lstZutaten");
    liste.appendChild(createEmptyZutatRow());
}

function createEmptyZutatRow() {
    let row = createBorder()
    row.id = getRandomId()

    let content = createDiv("row")
    content.appendChild(createZutatColumnMenge())
    content.appendChild(createZutatColumnEinheit())
    content.appendChild(createZutatColumnName())
    content.appendChild(createRemoveButton("lstZutaten", row.id))

    row.appendChild(content)
    return row
}

function createZutatColumnMenge() {
    // Spalte div erstellen
    let columnDiv = createDiv("col-6 col-md-2 pr-0");

    // input-field erstellen
    let input = createTextInput("Menge", 5);

    // label für das Input-Field konfigurieren
    let label = createHiddenLabel();
    input.id = getRandomId();
    label.htmlFor = input.id;

    // Label + Input-Field dem div hinzufügen
    columnDiv.appendChild(label);
    columnDiv.appendChild(input);

    return columnDiv;
}
function createZutatColumnEinheit() {
    // Spalte div erstellen
    let columnDiv = createDiv("col-6 col-md-2");

    // input-field erstellen
    let input = createTextInput("Einheit", 10);

    // label für das Input-Field konfigurieren
    let label = createHiddenLabel();
    input.id = getRandomId();
    label.htmlFor = input.id;

    // Label + Input-Field dem div hinzufügen
    columnDiv.appendChild(label);
    columnDiv.appendChild(input);

    return columnDiv;
}
function createZutatColumnName() {
    // Spalte div erstellen
    let columnDiv = createDiv("col-9 col-md-6 pr-0");

    // input-field erstellen
    let input = createTextInput("Zutatenname", 100);

    // label für das Input-Field konfigurieren
    let label = createHiddenLabel();
    input.id = getRandomId();
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
    };

    columnDiv.appendChild(buttonDiv);

    return columnDiv;
}

function addTask() {
    let liste = document.getElementById("lstTasks");
    liste.appendChild(createEmptyTaskRow());
}

function createEmptyTaskRow() {
    let row = createBorder()
    row.id = getRandomId()

    //let content = createDiv("row")
    row.appendChild(createTaskHeader(row.id))
    row.appendChild(createTaskDescription())

    //row.appendChild(content)
    return row
}

function createTaskHeader($id) {
    let column1 = createDiv("col-9 col-md-10 pr-0")

    let input = createTextInput("Vorbereitung, Zubereitung, Anweisungen", 2000)
    input.id = getRandomId()

     let label = createHiddenLabel()
    label.htmlFor = input.id

    column1.appendChild(label)
    column1.appendChild(input)



    let row = createDiv("row")
    row.appendChild(column1)
    row.appendChild(createRemoveButton("lstTasks", $id))

    return row;
}

function createTaskDescription() {
    // Spalte div erstellen
    let divDesc = createDiv("");

    // input-field erstellen
    let input = createTextAreaInput("Arbeitsschritt beschreiben", 2000);

    // label für das Input-Field konfigurieren
    let label = createHiddenLabel();
    input.id = getRandomId();
    label.htmlFor = input.id;

    // Label + Input-Field dem div hinzufügen
    divDesc.appendChild(label);
    divDesc.appendChild(input);

    return divDesc;
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