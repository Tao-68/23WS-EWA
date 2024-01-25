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
        const multiplier = parseInt(option.text.split(' ')[1].substring(1)); // the name looks like "Salami x2" so we take the 2nd part and remove the x
        const pizzaPrice = pizzaPriceElement.value;
        total = total + pizzaPrice * multiplier;
      }
    }
    totalPriceDiv.innerText = `Gesamtpreis: ${total.toFixed(2)} €`;
    totalPriceDiv.style.display = 'block';
    submitOrder.disabled = pizzaSelect.selectedOptions.length === 0 || addressInput.value.length === 0;
  }
  pizzaSelect.addEventListener('change', updateTotalPrice);
  
  pizzaOrderForm.addEventListener('submit', () => {
    if (submitOrder.disabled || pizzaSelect.options.length === 0 || addressInput.value.length === 0) 
      //console.log('Please select at least 1 Pizza and provide a valid address to place an order.');
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
    
  const pizzaDivs = document.querySelectorAll('div[id="pizzaImages"]');
  pizzaDivs.forEach((pizzaDiv) => {
    const pizzaImage = pizzaDiv.querySelector('img');
    pizzaImage.addEventListener('click', () => {
      const pizzaName = pizzaImage.alt;
      const existingOption = Array.from(pizzaSelect.options).find(option => option.title === pizzaImage.getAttribute('data-price'));
      if (existingOption) 
      {
        pizzaDict[existingOption.title]= pizzaDict[existingOption.title]+1;
        existingOption.text = `${pizzaName} x${pizzaDict[existingOption.title]}`;
      } 
      else 
      {
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

  