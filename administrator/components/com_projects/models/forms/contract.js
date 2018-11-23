'use strict';
window.onload = function () {
    setNumber();
};

function removeStand(standID) {
    fetch('/administrator/index.php?option=com_projects&task=contracts.removeStand&id=' + standID)
        .then(function (response) {
            return response.json();
        })
        .then(function (text) {
            if (text.data.result !== 1)
            {
                alert(text.data.message);
            }
            else
            {
                document.querySelector('#row_stand_' + standID).remove();
            }
        })
        .catch(function (error) {
            console.log('Request failed', error);
        });
}

function setNumber() {
    var status = document.getElementById("jform_status");
    var tip = status.options[status.selectedIndex].value;
    if (tip === '5' || tip === '6') unlockParent(); else lockParent();
}

function unlockParent() {
    jQuery('#jform_parentID').prop('disabled', false).trigger("liszt:updated");
}
function lockParent() {
    jQuery('#jform_parentID').val('').prop('disabled', true).trigger("liszt:updated");
}
function closeTask(id) {
    var result = document.querySelector("input[name='result_" + id + "']").value;
    fetch('/administrator/index.php?option=com_projects&task=todos.close&id=' + id + '&result=' + result)
        .then(function (response) {
            return response.json();
        })
        .then(function (text) {
            var td = document.querySelector('.resultTodo_' + id);
            td.innerText = text.data.dat + ": " + text.data.user+ " " + result;
        })
        .catch(function (error) {
            console.log('Request failed', error);
        });
}
function getSum(id, currency) {
    var sum = 0;
    var field = parseFloat(document.querySelector("#price_" + id).dataset.cost);
    var value = parseInt(document.querySelector("#price_" + id).value);
    sum = field * value;
    field = document.querySelector("#value2_" + id);
    if (field !== null)
    {
        sum = sum * parseInt(field.value);
    }
    field = document.querySelector("#factor_" + id);
    if (field !== null)
    {
        sum = sum * parseFloat((100 - parseInt(field.value)) / 100);
    }
    field = document.querySelector("#markup_" + id);
    if (field !== null)
    {
        sum = sum * parseFloat((100 + parseInt(field.options[field.selectedIndex].value)) / 100);
    }
    sum = Math.round(sum);
    document.querySelector("#sum_" + id).innerHTML = sum;
    document.querySelector("#currency_" + id).innerHTML = currency;
    var amounts = document.querySelectorAll(".amounts");
    sum = 0;
    for (var i = 0; i < amounts.length; i++)
    {
        if (amounts[i].innerText !== "") sum = sum + parseFloat(amounts[i].innerText);
    }
    document.querySelector("#sum_amount").innerHTML = sum + " " + currency;
}