document.addEventListener('DOMContentLoaded', () => {
 "use strict";
  var pizzaDict={ "8.57":1, "12.5":1,"11.99":1, "9.99":1, "8.5":1,"14.4":1,"13.99":1,"7.5":1,"12.99":1, "11.39":1}; //is a dictionary that maps the price of a pizza to the number of times it has been added to the cart

  const pizzaOrderForm = document.forms['pizzaOrderForm'];
  const submitOrder = document.querySelector('input[name="submitOrder"]'); 
  //const submitOrder = document.getElementsByName('submitOrder')[0]; //since we get a NodeListOf<>, we have to use indexing here
  submitOrder.disabled = true ;
  
  //get the very first input element with the name 'warenkorb[]' (querySelector returns the first element that matches the criteria unlike getElementsByName)
  const pizzaSelect = document.querySelector('select[name="warenkorb[]"]');
  const totalPriceDiv = document.querySelector('div[id="gesamtPreis"]');
  const addressInput = document.querySelector('input[name="address"]');
  
  function updateTotalPrice()
  {
    if (totalPriceDiv.innerText.trim() === 'Gesamtpreis: 0 €') 
      totalPriceDiv.style.display = 'none';
  
    var total = 0;
    const allOptions = pizzaSelect.options;
    for (const option of allOptions) 
    {
      const pizzaPriceElement = document.querySelector(`input[name="singlePizzaPrice"][value="${option.title}"]`);
      if (pizzaPriceElement) 
      {
        const multiplier = parseInt(option.text.split(' ')[1].substring(1)); // the name is smth like Salami x2 so we take the 2nd part and remove the x
        const pizzaPrice = pizzaPriceElement.value;
        total = total + pizzaPrice * multiplier;
      }
    }
    totalPriceDiv.innerText = `Gesamtpreis: ${total.toFixed(2)} €`;
    totalPriceDiv.style.display = 'block';
    submitOrder.disabled = pizzaSelect.selectedOptions.length === 0 || addressInput.value.length === 0;
  }
  pizzaSelect.addEventListener('change', updateTotalPrice);
  
  //When a user submits a form, it triggers the forms submit event which is not only triggered by clicking the submitOrder button but also by other means such as 
  //pressing the Enter key when this form is in focus, so by attaching the event listener to the forms submit event and not the submitOrder button itself, we ensure that 
  //the validation logic is executed consistently whenever the form is submitted regardless of the means by which it is submitted
  pizzaOrderForm.addEventListener('submit', () => {
    if (submitOrder.disabled || pizzaSelect.options.length === 0 || addressInput.value.length === 0) 
      alert('Please select at least 1 Pizza and provide a valid address to place an order.');   
  });
  
  const deleteButton = document.querySelector('input[name="delete"]');
    deleteButton.addEventListener('click', () => {
        // Remove the selected options
        const selectedOptions = pizzaSelect.selectedOptions;
        for (const option of selectedOptions) 
        {     
          pizzaDict[option.title]=pizzaDict[option.title]-1;     
          if (pizzaDict[option.title] === 0) 
          {
            pizzaSelect.remove(pizzaSelect.selectedIndex);
            delete pizzaDict[option.title];
          }
          else
          {
            option.text = `${option.text.split(' ')[0]} x${pizzaDict[option.title]}`;
          }
        }
        updateTotalPrice();
    }); 

    addressInput.addEventListener('input', ()=> {
      submitOrder.disabled = pizzaSelect.selectedOptions.length === 0 || addressInput.value.length === 0;
    });
  
    const deleteAllButton = document.querySelector('input[name="deleteAll"]');
    deleteAllButton.addEventListener('click', () => {
        while (pizzaSelect.options.length > 0) 
          pizzaSelect.remove(0);
        
        pizzaOrderForm.reset();
        totalPriceDiv.innerText = 'Gesamtpreis: 0 €';
        submitOrder.disabled = true;

        for (const key in pizzaDict) 
        {
          if (pizzaDict.hasOwnProperty(key)) 
            pizzaDict[key] = 1;
            //console.log(key, pizzaDict[key]);
        }
    });
    

  //if the user clicks on an image, search for the corresponding pizza with the same name as the image in the select element and then add it to the cart
  //problem: cant add the same element multiple times to the cart
  //solution: selected the div as a whole so we got more info about the pizza like its price,name etc.
  const pizzaDivs = document.querySelectorAll('div[id="pizzaImages"]');
  pizzaDivs.forEach((pizzaDiv) => {
    const pizzaImage = pizzaDiv.querySelector('img');
    pizzaImage.addEventListener('click', () => {
      const pizzaName = pizzaImage.alt;
      const existingOption = Array.from(pizzaSelect.options).find(option => option.title === pizzaImage.getAttribute('data-price'));
      //console.log(existingOption);
      if (existingOption) 
      {
        //console.log(existingOption.title);
        pizzaDict[existingOption.title]= pizzaDict[existingOption.title]+1;
        existingOption.text = `${pizzaName} x${pizzaDict[existingOption.title]}`;
      } 
      else 
      {
        //console.log('in new');
        const newOption = document.createElement('option');
        newOption.value = pizzaDiv.getAttribute('data-value');
        newOption.text = pizzaName + ' x1';
        newOption.title = pizzaDiv.getAttribute('data-price');
        newOption.selected = true;
        newOption.hidden = false;
        pizzaSelect.add(newOption);
      }
      updateTotalPrice();
    });
  });

  const pizzaSelector = document.getElementById('pizzaSelector');
  pizzaSelector.addEventListener('mouseenter', () => {
        pizzaSelector.size = pizzaSelect.options.length;
    });

    pizzaSelector.addEventListener('mouseleave', () => {
        pizzaSelector.size = 1;
    });
});

// function contrastGesamtPreisWithBackgroundColor()
//   {
//     const gesamtPreisElement = document.getElementById('gesamtPreis');
//     const bodyBackgroundColor = document.body.style.backgroundColor;
//     const isBackgroundColorLight = isColorLight(bodyBackgroundColor);
//     console.log("Body Background Color:", bodyBackgroundColor);
//     console.log("Is Background Color Light:", isBackgroundColorLight);
//     gesamtPreisElement.style.color = isBackgroundColorLight ? "#000000" : "#FFFFFF";
//   }
    
//   function isColorLight(color) 
//   {
//     var rgb = getColorRgb(color);
//     //console.log("rgb:", rgb);
//     var r = (rgb >> 16) & 0xff;
//    // console.log("r:", r);
//     var g = (rgb >> 8) & 0xff;
//     //console.log("g:", g);
//     var b = rgb & 0xff; // No need to shift here
//     //console.log("b:", b);
//     var average = (r + g + b) / 3;
//     //console.log("avg:", average);
//     return average > 100;
//   }
  
//   function getColorRgb(color) 
//   {
//     if (color.startsWith("rgb")) 
//     {
//       const rgbArray = color.match(/\d+/g);
//       return (parseInt(rgbArray[0]) << 16) | (parseInt(rgbArray[1]) << 8) | parseInt(rgbArray[2]);
//     } 
//     else 
//     {
//       const tempDiv = document.createElement("div");
//       tempDiv.style.color = color;
//       document.body.appendChild(tempDiv);
//       const rgb = getComputedStyle(tempDiv).color.match(/\d+/g);
//       document.body.removeChild(tempDiv);
//       return (parseInt(rgb[0]) << 16) | (parseInt(rgb[1]) << 8) | parseInt(rgb[2]);
//     }
//   }

// window.onload = () => {
//   contrastGesamtPreisWithBackgroundColor();
//   window.setInterval(contrastGesamtPreisWithBackgroundColor, 1000);
// }
  