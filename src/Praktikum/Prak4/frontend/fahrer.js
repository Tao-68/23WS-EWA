document.addEventListener("DOMContentLoaded", () => {
    "use strict";
    var forms = document.querySelectorAll("form[data-name]");
    
    forms.forEach((form) => {
        form.addEventListener("change", () => {
            form.submit();
        });
    });
});

function processFahrer() 
{
    "use strict";
    var xhr = new XMLHttpRequest();
    //ajax makes a get request, fahrerstatus.php receives it, executes its fahrer::main() function where the generateView will echo the JSON data which is then received as ajax response    
    xhr.open('GET', 'FahrerStatus.php', true);
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200 && xhr.responseText != null) 
        {
            var data = JSON.parse(xhr.responseText);
            updateDOM(data);          
        }
    };
    xhr.send(null);
}

function updateDOM(data) 
{ 
    "use strict";
    if (data && data.length > 0) 
    {
        //item is a dictionary(key-value pair) in the data array
        data.forEach((item) => { 
            var orderedArticleId = item.ordered_article_id; 
            var orderingId=item.ordering_id;
            var pizzaName = item.name;
            var statusCode = item.status === null ? null : parseInt(item.status);
            updateOrCreateDOM(statusCode,orderedArticleId,pizzaName,orderingId);
        });
    }
}

//when new orders come in we might have to add to the DOM but for now we will just update the status
function updateOrCreateDOM(statusCode, ordered_article_id, pizzaName,orderingId) 
{
    "use strict";
    var searchedForm = document.querySelector(`form[id="fahrerForm${orderingId}"]`);
    if (searchedForm) 
    {
        var radioName = `status[${statusCode}]`;
        var radioElement = searchedForm.querySelector(`input[name="${radioName}"][value="${statusCode.toString()}"]`);
        if (radioElement) 
            radioElement.checked = true;
        else 
            console.error(`Radio button not found for ${pizzaName} with statusCode ${statusCode}`);     
    } 
    else 
    {
        console.error(`Form not found for ${pizzaName} with ordered_article_id ${ordered_article_id}`);
    }
}

//once the page has loaded fully, call the process function
window.onload = () => {
    processFahrer(); 
    window.setInterval(processBacker, 2000);
};