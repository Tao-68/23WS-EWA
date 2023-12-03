function process() 
{
    console.log(Math.random());
    var xhr = new XMLHttpRequest();
    //ajax makes a get request, kundenstatus.php receives it, executes its Kunde::main() function where the generateView will echo the JSON data which is then received as ajax response    
    xhr.open('GET', 'KundenStatus.php', true);
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200 && xhr.responseText != null) 
        {
            //converts the JSON string into an array of dictionary(key-value pairs).
            var data = JSON.parse(xhr.responseText);
            updateDOM(data);          
        }
    };
    xhr.send(null);
}

function updateDOM(data) 
{ 
    if (data && data.length > 0) 
    {
        //item is a dictionary(key-value pair) in the data array
        data.forEach((item) => {
            var orderingId = item.ordering_id; 
            var ordered_article_id = item.ordered_article_id; 
            var pizzaName = item.name;    
            var statusCode = item.status === null ? null : parseInt(item.status);
            updateOrCreateDOM(orderingId, pizzaName, statusCode,ordered_article_id);
        });
    }
}

function updateOrCreateDOM(orderId, pizzaName, statusCode, ordered_article_id) 
{
    var orderDiv = document.querySelector(`div[id="order_${orderId}"]`);
    var existingHiddenInput = document.querySelector(`input[value="${ordered_article_id}"]`);

    // if (existingHiddenInput)        
    //     return;
    
    if (document.querySelector('div[id="dogDiv"]')) 
        document.querySelector('div[id="dogDiv"]').style.display = 'none';
    
    if (!orderDiv || !existingHiddenInput) 
    {
        orderDiv = document.createElement('div');
        orderDiv.id = `order_${orderId}`;
        document.body.appendChild(orderDiv);
        
        var orderParagraph = document.createElement('p');
        orderParagraph.textContent = `Order #${orderId}`;
        orderDiv.appendChild(orderParagraph);

        var pizzaParagraph = document.createElement('p');
        pizzaParagraph.textContent = `Pizza: ${pizzaName}`;
        orderDiv.appendChild(pizzaParagraph);

        var statusParagraph = document.createElement('p');
        statusParagraph.textContent = `Status: ${getStatusText(statusCode)}`;
        statusParagraph.setAttribute(`data-status-${ordered_article_id}`, ordered_article_id);
        orderDiv.appendChild(statusParagraph);

        var lineBreak = document.createElement('br');
        orderDiv.appendChild(lineBreak);
        
        var hiddenInput = document.createElement('input');
        hiddenInput.value = ordered_article_id;
        //console.log(ordered_article_id);
        hiddenInput.type = 'hidden';
        orderDiv.appendChild(hiddenInput);
    }

    var statusParagraph = orderDiv.querySelector(`p[data-status-${ordered_article_id}]`);
    statusParagraph.textContent = `Status: ${getStatusText(statusCode)}`;
    //console.log(`end ${Math.random()}`);
}


function getStatusText(statusCode) 
{
    const statusDict = 
    {
        0: "Zubereitung",
        1: "Im Ofen",
        2: "Fertig gebackt",
        3: "Warte auf Abholung",
        4: "Unterwegs",
        5: "Geliefert",
    };
    return statusDict[statusCode] || "Unknown Status";
}

//once the page has loaded fully, call the process function
window.onload = () => {
    process(); 
    window.setInterval(process, 2000);
};


