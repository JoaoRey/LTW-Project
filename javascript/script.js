const searchItems = document.querySelector('#searchitems');

if (searchItems) {
  searchItems.addEventListener('input', async function() {
    const response = await fetch('../api/api_items.php?search=' + this.value);
    const items = await response.json();

    const section = document.querySelector('#items');
    section.innerHTML = '';

    for (const item of items) {
      const article = document.createElement('article');
      const img = document.createElement('img');
      img.src = 'https://picsum.photos/200?' + item.ItemId; 
      const link = document.createElement('a');
      link.href = '../pages/item.php?id=' + item.ItemId; 
      link.textContent = item.Title; 
      article.appendChild(img);
      article.appendChild(link);
      section.appendChild(article);
    }
  });
}


function previewImages(event, startIndex) {
  const files = event.target.files;
  
  for (let i = 0; i < files.length; i++) {
      const file = files[i];
      const reader = new FileReader();
      const previewImage = document.getElementById(`preview-image-${startIndex + i}`);

      reader.onloadend = function () {
          previewImage.style.display = "block";
          previewImage.src = reader.result;
      }

      if (file) {
          reader.readAsDataURL(file);
      } else {
          previewImage.style.display = "none"; // Hide the preview image if no file is selected
          previewImage.src = "";
      }
  }
}

var imgIndex = 0; // Initial index

    function leftbuttPost() {
        var images = document.getElementsByClassName('post-image-product');
        images[imgIndex].classList.remove('active');
        imgIndex = (imgIndex - 1 + images.length) % images.length;
        images[imgIndex].classList.add('active');
    }

    function rightbuttPost() {
        var images = document.getElementsByClassName('post-image-product');
        images[imgIndex].classList.remove('active');
        imgIndex = (imgIndex + 1) % images.length;
        images[imgIndex].classList.add('active');
    }

    
  
  
  document.addEventListener('DOMContentLoaded', function() {
    var paymentMethodElement = document.getElementById('payment_method');
    
    if (paymentMethodElement) {
        paymentMethodElement.addEventListener('change', function() {
            var paymentMethod = this.value;
            
            var allFields = document.querySelectorAll('#credit_card_fields input, #paypal_fields input');
            allFields.forEach(function(field) {
                field.removeAttribute('required');
            });
            
            if (paymentMethod === 'credit_card') {
                document.getElementById('credit_card_fields').style.display = 'block';
                document.getElementById('paypal_fields').style.display = 'none';
                
                var creditCardFields = document.querySelectorAll('#credit_card_fields input');
                creditCardFields.forEach(function(field) {
                    field.setAttribute('required', '');
                });
            } else if (paymentMethod === 'paypal') {
                document.getElementById('credit_card_fields').style.display = 'none';
                document.getElementById('paypal_fields').style.display = 'block';
                
                var paypalFields = document.querySelectorAll('#paypal_fields input');
                paypalFields.forEach(function(field) {
                    field.setAttribute('required', '');
                });
            } else {
                document.getElementById('credit_card_fields').style.display = 'none';
                document.getElementById('paypal_fields').style.display = 'none';
            }
        });
    }
});


// Function to scroll the messages container to the bottom
function scrollMessagesToBottom() {
  var messagesContainer = document.getElementById('messages-container');
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Call the function to scroll to the bottom once the messages are loaded
window.onload = function() {
  scrollMessagesToBottom();
};

function updateCharacterCount() {
    const textarea = document.getElementById('description-input');
    const charCount = document.getElementById('charCount');
    const remaining = 40 - textarea.value.length;
    charCount.textContent = `${remaining} characters remaining`;
}