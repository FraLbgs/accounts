/* Page add.php */

if(document.getElementById("submit") !== null){
    document.getElementById("submit").addEventListener("click", function(e){
        putOperation(document.getElementById("name").value, 
        document.getElementById("date").value, 
        document.getElementById("amount").value, 
        document.getElementById("category").value).then(apiResponse => {
            if (!apiResponse.result) {
                console.error('Problème avec la requête.');
                return;
            }
            else document.getElementById("msg-confirm").innerText = apiResponse.msg;
        });
    });
}


/* Page index.php */

if(document.getElementById("modify-transaction") !== null){
    document.querySelectorAll(".bi-pencil").forEach(btn => {
        btn.addEventListener('click', e => {
            const idT = e.target.dataset.transaction;
            const idC = e.target.dataset.cat;
            const tr = e.target.closest("tr");
            const name = tr.querySelector("[data-name]").dataset.name;
            const date = tr.querySelector("time").getAttribute("datetime");
            const amount = parseInt(tr.querySelector("span").innerText);
            const form = createForm(idT, idC, name, date, amount);
            document.querySelector("tbody").insertBefore(form,tr);
            form.addEventListener('submit', e => {
                e.preventDefault();
                modifyTransaction(e.target.closest("tr").dataset.formId,
                    form.querySelector('select[name="category"]').value,
                    form.querySelector('input[name="name"]').value,
                    form.querySelector('input[name="date"]').value,
                    form.querySelector('input[name="amount"]').value
                    )
                .then(res => {
                    // console.log(res);
                    if (res.result){
                        updateTransaction(res.idT, res.idC, res.name, res.date, res.amount, res.classC);
                        document.getElementById("msg").innerText = res.msg;
                        setTimeout(() => document.getElementById("msg").innerText = "", 3000);
                    }
                    else console.error('Erreur lors de la modification.');
                    
                    form.remove();
                });
            });
        });
    });
    
    document.querySelectorAll(".bi-trash").forEach(btn => {
        btn.addEventListener('click', e => {
            const idT = e.target.dataset.delete;
            console.log("idT",idT);
            const tr = e.target.closest("tr");
    
            deleteTransaction(idT).then(res => {
                    console.log(res);
                    if (res.result){
                        removeTransaction(tr);
                        document.getElementById("msg").innerText = res.msg;
                        setTimeout(() => document.getElementById("msg").innerText = "", 3000);
                    }
                    else console.error('Erreur lors de la modification.');
            });
        });
    });
}

function deleteTransaction(idT){

    return callAPI('DELETE', {
        action: 'delete',
        idT: idT
    });
}

function removeTransaction(tr){
    tr.remove();
}

function updateTransaction(idT, idC, name, date, amount, classC) {
    const tr = document.getElementById(idT);
    console.log(tr.querySelector("[data-cat]"));
    tr.querySelector("[data-cat]").dataset.cat = idC;
    tr.querySelector("[data-name]").dataset.name = name;
    tr.querySelector("[data-name]").innerHTML = `<time datetime='${date}' class='d-block fst-italic fw-light'>`+ new Date(date).toLocaleDateString()+`</time>${name}`;
    tr.querySelector("span").innerText = parseFloat(amount).toFixed(2);
    if(idC !== null) tr.querySelector(".ps-3").innerHTML = "<i class='bi bi-"+classC+" fs-3'></i>";
    else tr.querySelector(".ps-3").innerHTML = "";
}

function modifyTransaction(idT, idC, name, date, amount) {
    return callAPI('PUT', {
        action: 'modify',
        idT: idT,
        idC: idC,
        name: name,
        date: date,
        amount: amount,
        token: getCsrfToken()
    });
}

function createForm(idT, idC, name, date, amount) {
    const form = document.querySelector("#modify-transaction").content.cloneNode(true);
    console.log(idT, idC, name, date, amount);
    console.log(form.querySelector('[name="category"] option[value="'+idC+'"]'));

    form.querySelector('[name="name"]').value = name;
    form.querySelector('[name="date"]').value = date;
    form.querySelector('[name="amount"]').value = amount;
    // form.querySelector('[name="category"] option[value="'+idC+'"]') = idC;
    form.querySelector('[name="category"] option[value="'+idC+'"]').selected = true;
    form.querySelector('tr').dataset.formId = idT;
    return form.querySelector('tr');
}

function getCsrfToken() {
    return document.querySelector('#token-csrf').value;
}

function putOperation(name, date, amount, category) {
    return callAPI('POST', {
        action: 'addOpe',
        name: name,
        date: date,
        amount: amount,
        category: category,
        token: getCsrfToken()
    });
}

async function callAPI(method, data) {
    try {
        const response = await fetch("api.php", {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        return response.json();
    }
    catch (error) {
        console.error("Unable to load datas from the server : " + error);
    }
}


/* Page categories.php */

if(document.getElementById("add-category") !== null){
    document.getElementById("add-category").addEventListener("click", function(e){
        e.preventDefault();
        const name = document.getElementById("name").value;
        const icon = document.getElementById("icon").value;
        addCategory(name, icon).then(res => {
            // console.log(res);
            if (res.result){
                updateCategories(res.idC, name, icon);
                document.getElementById("msg").innerText = res.msg;
                setTimeout(() => document.getElementById("msg").innerText = "", 3000);
            }
            else console.error('Erreur lors de lajout.');
        });
    });
}

function addCategory(name, icon){
    return callAPI('POST', {
        action: 'addCat',
        name: name,
        icon: icon,
        token: getCsrfToken()
    });
}

function updateCategories(idC, name, icon){
    const li = document.createElement("li");
    li.className = "list-group-item d-flex justify-content-between align-items-center";
    li.id = idC;
    li.innerHTML = displayCategory(idC, name, icon);
    document.querySelector(".list-group-flush").appendChild(li);
}

function displayCategory(idC, name, icon, totalOpe = 0){
    return `<div>
        <i class='bi bi-${icon} fs-3'></i>
        &nbsp;
        ${name}
        &nbsp;
        <span class=' badge bg-secondary'>${totalOpe} opérations</span>
    </div>
    <div>
        <a href='#' class='btn btn-outline-primary btn-sm rounded-circle'>
            <i class='bi bi-pencil' data-cat-id='${idC}' data-name='${name}' data-icon='${icon}'></i>
        </a>
        <a href='#' class='btn btn-outline-danger btn-sm rounded-circle'>
            <i class='bi bi-trash' data-cat-id='${idC}'></i>
        </a>
    </div>`;
}

if(document.getElementById("modify-category") !== null){
    document.querySelectorAll(".bi-pencil").forEach(btn => {
        btn.addEventListener('click', e => {
            const li = e.target.closest("li");
            const idC = e.target.dataset.catId;
            const name = e.target.dataset.name;
            const icon = e.target.dataset.icon;
            const form = createFormCat(idC, name, icon);
            document.querySelector(".list-group-flush").insertBefore(form,li);
            form.addEventListener('submit', e => {
                e.preventDefault();
                modifyCategory(e.target.closest("li").dataset.formId,
                    form.querySelector('input[name="name"]').value,
                    form.querySelector('input[name="icon"]').value
                    )
                .then(res => {
                    // console.log(res);
                    if (res.result){
                        updateCategory(res.idC, res.name, res.icon, res.totalOpe);
                        document.getElementById("msg").innerText = res.msg;
                        setTimeout(() => document.getElementById("msg").innerText = "", 3000);
                    }
                    else console.error('Erreur lors de la modification.');
                    
                    form.remove();
                });
            });
        });
    });
}

function createFormCat(idC, name, icon) {
    const form = document.querySelector("#modify-category").content.cloneNode(true);
    console.log(name, icon);
    form.querySelector('[name="name"]').value = name;
    form.querySelector('[name="icon"]').value = icon;
    form.querySelector('li').dataset.formId = idC;
    return form.querySelector('li');
}

function modifyCategory(idC, name, icon){
    return callAPI('PUT', {
        action: 'modifyCat',
        idC: idC,
        name: name,
        icon: icon,
        token: getCsrfToken()
    });
}

function updateCategory(idC, name, icon, totalOpe){
    const li = document.getElementById(idC);
    li.innerHTML = displayCategory(idC, name, icon, totalOpe);
}