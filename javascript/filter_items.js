var priceSlider = document.getElementById("price-slider");
var minPriceDisplay = document.getElementById("min-price");
var maxPriceDisplay = document.getElementById("max-price");
var priceDisplay = document.getElementById("price-display");

noUiSlider.create(priceSlider, {
    start: [0, 10000], 
    connect: true, 
    step: 10, 
    range: {
        'min': 0,
        'max': 10000
    }
});

priceSlider.noUiSlider.on('update', function (values, handle) {
    var value = values[handle];
    
    if (handle) {
        maxPriceDisplay.textContent = "€" + value;
    } else {
        minPriceDisplay.textContent = "€" + value;
    }

    priceDisplay.textContent = "€" + minPriceDisplay.textContent.substring(1) + " - €" + maxPriceDisplay.textContent.substring(1);
});

function applyFilters() {
    var selectedCategory = document.getElementById("category-select").value;
    var selectedBrand = document.getElementById("brand-select").value;
    var selectedCondition = document.getElementById("condition-select").value;
    var selectedSize = document.getElementById("size-select").value;
    var selectedModel = document.getElementById("model-select").value;
    var minPrice = minPriceDisplay.textContent.substring(1); 
    var maxPrice = maxPriceDisplay.textContent.substring(1);
    
    if (selectedCategory === "" && selectedBrand === "" && selectedCondition === "" && selectedSize === "" && selectedModel === "" && minPrice === "0" && maxPrice === "10000") {
        return;
    }
    
    var url = "/pages/filter.php?";
    if (selectedCategory) {
        url += "category=" + selectedCategory + "&";
    }
    if (selectedBrand) {
        url += "brand=" + selectedBrand + "&";
    }
    if (selectedCondition) {
        url += "condition=" + selectedCondition + "&";
    }
    if (selectedSize) {
        url += "size=" + selectedSize + "&";
    }
    if (selectedModel) {
        url += "model=" + selectedModel + "&";
    }
    url += "minPrice=" + minPrice + "&";
    url += "maxPrice=" + maxPrice;
    
    window.location.href = url;
}

function filterItems(category) {
    
    window.location.href = `/pages/filter.php?category=${category}`;
}

function populateSelect(selectId, data) {
    var selectElement = document.getElementById(selectId);
    selectElement.innerHTML = ''; 
    
    if (data.length > 0) {
        var defaultOption = document.createElement('option');
        defaultOption.value = "";
        defaultOption.textContent = "Selecione...";
        selectElement.appendChild(defaultOption);
    }
    data.forEach(function(item) {
        var option = document.createElement('option');
        option.textContent = item;
        selectElement.appendChild(option);
    });
}



fetch('/templates/route.php?action=get-categories')
    .then(response => response.json())
    .then(data => populateSelect('category-select', data));

fetch('/templates/route.php?action=get-brands')
    .then(response => response.json())
    .then(data => populateSelect('brand-select', data));

fetch('/templates/route.php?action=get-conditions')
    .then(response => response.json())
    .then(data => populateSelect('condition-select', data));

fetch('/templates/route.php?action=get-sizes')
    .then(response => response.json())
    .then(data => populateSelect('size-select', data));

fetch('/templates/route.php?action=get-models')
    .then(response => response.json())
    .then(data => populateSelect('model-select', data));


//don't allow duplicate categories
document.addEventListener('DOMContentLoaded', () => {
    // Check if category elements exist before adding event listeners
    const category1 = document.getElementById('category1');
    const category2 = document.getElementById('category2');
    const category3 = document.getElementById('category3');

    if (category1 && category2 && category3) {
        function checkDuplicateCategories() {
            const selectedCategories = [category1, category2, category3];
            const uniqueCategories = new Set(selectedCategories.map(cat => cat.value).filter(cat => cat !== 'none'));

            if (uniqueCategories.size !== selectedCategories.filter(cat => cat.value !== 'none').length) {
                alert('You cannot select the same category more than once.');
                selectedCategories.forEach(cat => {
                    if (cat.value !== 'none' && selectedCategories.filter(c => c.value === cat.value).length > 1) {
                        cat.value = 'none'; // Reset the duplicated selection
                    }
                });
                return false;
            }
            return true;
        }

        category1.addEventListener('change', checkDuplicateCategories);
        category2.addEventListener('change', checkDuplicateCategories);
        category3.addEventListener('change', checkDuplicateCategories);

        document.querySelector('form').addEventListener('submit', (event) => {
            if (!checkDuplicateCategories()) {
                event.preventDefault();
            }
        });
    }

    // Scroll messages to bottom if the element exists
    function scrollMessagesToBottom() {
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    // Ensure scrollMessagesToBottom runs only if the page is fully loaded and the element exists
    window.onload = () => {
        scrollMessagesToBottom();
    };
});


function openSearchTab() {
    var searchTab = document.getElementById("search-tab");
    if (searchTab.style.display === "none") {
        searchTab.style.display = "block";
    } else {
        searchTab.style.display = "none";
    }
}
document.addEventListener("DOMContentLoaded", function() {
    var filterButton = document.getElementById("filter-search-tab");

    // Toggle the display of the filter box when the button is clicked
    filterButton.addEventListener("click", function() {
        var filterBox = document.getElementById("filter-box");
        
        // Ensure that the filterBox is initially hidden
        if (filterBox.style.display === "" || filterBox.style.display === "none") {
            filterBox.style.display = "block";
        } else {
            filterBox.style.display = "none";
        }
    });
});
