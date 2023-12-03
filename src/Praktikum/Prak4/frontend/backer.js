document.addEventListener("DOMContentLoaded", () => {
    var forms = document.querySelectorAll("form[data-name]");
    forms.forEach((form) => {
        form.addEventListener("change", () => {
            form.submit();
        });
    });
});

// function processBacker() 
// {
//     //console.log(Math.random());
//     var xhr = new XMLHttpRequest();
//     //ajax makes a get request, bäckertatus.php receives it, executes its Bäcker::main() function where the generateView will echo the JSON data which is then received as ajax response    
//     xhr.open('GET', 'BäckerStatus.php', true);
//     xhr.onreadystatechange = () => {
//         if (xhr.readyState === 4 && xhr.status === 200 && xhr.responseText != null) 
//         {
//             var data = JSON.parse(xhr.responseText);
//             updateDOM(data);          
//         }
//     };
//     xhr.send(null);
// }

// function updateDOM(data) 
// { 
//     //console.log(`update DOM ${Math.random()}`);
//     if (data && data.length > 0) 
//     {
//         //console.log(data.length);
//         //item is a dictionary(key-value pair) in the data array
//         data.forEach((item) => { 
//             var orderedArticleId = item.ordered_article_id; 
//             var pizzaName = item.name;
//             var statusCode = item.status === null ? null : parseInt(item.status);
//             updateOrCreateDOM(statusCode,orderedArticleId,pizzaName);
//         });
//     }
// }

// //when new orders come in we might have to add to the DOM 
// function updateOrCreateDOM(statusCode, ordered_article_id, pizzaName) {
//     var searchedForm = document.querySelector(`form[id="backerForm${ordered_article_id}"]`);
//     //console.log(searchedForm);
//     if (searchedForm) 
//     {
//         var radioName = `food_status${statusCode}`;
//         var radioElement = searchedForm.querySelector(`input[name="${radioName}"][value="${statusCode.toString()}"]`);
//         if (radioElement) 
//             radioElement.checked = true;
//         else 
//             console.error(`Radio button not found for ${pizzaName} with statusCode ${statusCode}`);
        
//     } 
//     else 
//     {
//         console.error(`Form not found for ${pizzaName} with ordered_article_id ${ordered_article_id}`);
//     }
// }


// //once the page has loaded fully, call the process function
// window.onload = () => {
//     processBacker(); 
//     window.setInterval(processBacker, 3000);
// };

