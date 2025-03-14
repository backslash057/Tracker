
let addTrans = document.querySelector("addButton");
let removeTrans = document.querySelector("addButton");


let transList = document.querySelector("transList");


function fetchTranscations() {
    transactions = [];

    fetch("/transactions.php")
    .then(response => response.json())
    .then(data => {

    })
    .catch(e => {
        console.log(e);
        transList.innerHTML = "Errer de chargement. Essayez de recharger la page"
    });


}


function addTransaction() {

}

function removeTransaction() {

}



fetchTransactions();