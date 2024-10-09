document.addEventListener('DOMContentLoaded', function() {
    // Process star ratings for each element with class 'stars'
    const stars = document.querySelectorAll('.stars');
    stars.forEach(star => {
        const rating = parseFloat(star.getAttribute('data-rating'));
        const filledStars = '★'.repeat(Math.floor(rating));
        const remainder = rating % 1;
        let starHTML = filledStars;
        if (remainder > 0) {
            const fraction = '&#x2605;'.repeat(Math.ceil(remainder * 2)); // Divide each star into 2 parts
            starHTML += '<span class="half-star">' + fraction + '</span>';
        }
        star.innerHTML = starHTML;
    });

    // Check if the element with id 'average-rating' exists
    const averageRatingElement = document.getElementById('average-rating');
    const averageStarsElement = document.getElementById('average-stars');
    if (averageRatingElement && averageStarsElement) {
        const averageRating = parseFloat(averageRatingElement.textContent);
        const filledAverageStars = '★'.repeat(Math.floor(averageRating));
        const averageRemainder = averageRating % 1;
        let averageStarHTML = filledAverageStars;
        if (averageRemainder > 0.25 && averageRemainder < 0.75) {
            const averageFraction = '&#x2605;'.repeat(Math.ceil(averageRemainder * 2)); 
            averageStarHTML += '<span class="half-star">' + averageFraction + '</span>';
        } else if (averageRemainder >= 0.75) {
            averageStarHTML += '★';
        }
        averageStarsElement.innerHTML = averageStarHTML;
    }
});





function toggleAdminSection() {
    var adminSection = document.getElementById("admin-section");
    var presentedProducts = document.getElementById("profile-presented");
    var profileEdit = document.getElementById("edit-profile-section");
    var changepass = document.getElementById("change-password");
    var purchaseHistory = document.getElementById("purchase-history");
    var reviewsSection = document.getElementById("reviews-section");
    reviewsSection.style.display = "none";
    adminSection.style.display = "block";
    presentedProducts.style.display = "none";
    profileEdit.style.display = "none";
    changepass.style.display = "none";
    purchaseHistory.style.display = "none";
    
}
function toggleProfileProd() {
    var adminSection = document.getElementById("admin-section");
    var presentedProducts = document.getElementById("profile-presented");
    var profileEdit = document.getElementById("edit-profile-section");
    var changepass = document.getElementById("change-password");
    var purchaseHistory = document.getElementById("purchase-history");
    var reviewsSection = document.getElementById("reviews-section");
    reviewsSection.style.display = "none";
    adminSection.style.display = "none";
    presentedProducts.style.display = "block";
    profileEdit.style.display = "none";
    changepass.style.display = "none";
    purchaseHistory.style.display = "none";
}
    function toggleReviewsSection() {
    var adminSection = document.getElementById("admin-section");
    var presentedProducts = document.getElementById("profile-presented");
    var profileEdit = document.getElementById("edit-profile-section");
    var changepass = document.getElementById("change-password");
    var purchaseHistory = document.getElementById("purchase-history");
    var reviewsSection = document.getElementById("reviews-section");

    adminSection.style.display = "none";
    presentedProducts.style.display = "none";
    profileEdit.style.display = "none";
    changepass.style.display = "none";
    purchaseHistory.style.display = "none";
    reviewsSection.style.display = "block";
}

function toggleEditProfile() {
    var adminSection = document.getElementById("admin-section");
    var presentedProducts = document.getElementById("profile-presented");
    var profileEdit = document.getElementById("edit-profile-section");
    var changepass = document.getElementById("change-password");
    var purchaseHistory = document.getElementById("purchase-history");
    var reviewsSection = document.getElementById("reviews-section");
    reviewsSection.style.display = "none";
    adminSection.style.display = "none";
    presentedProducts.style.display = "none";
    profileEdit.style.display = "block";
    changepass.style.display = "none";
    purchaseHistory.style.display = "none";
}

function toggleChangePass() {
    var adminSection = document.getElementById("admin-section");
    var presentedProducts = document.getElementById("profile-presented");
    var profileEdit = document.getElementById("edit-profile-section");
    var changepass = document.getElementById("change-password");
    var purchaseHistory = document.getElementById("purchase-history");
    var reviewsSection = document.getElementById("reviews-section");
    reviewsSection.style.display = "none";
    adminSection.style.display = "none";
    presentedProducts.style.display = "none";
    profileEdit.style.display = "none";
    changepass.style.display = "block";
    purchaseHistory.style.display = "none";
}
function togglePurchaseHistory() {
    var adminSection = document.getElementById("admin-section");
    var presentedProducts = document.getElementById("profile-presented");
    var profileEdit = document.getElementById("edit-profile-section");
    var changepass = document.getElementById("change-password");
    var purchaseHistory = document.getElementById("purchase-history");
    var reviewsSection = document.getElementById("reviews-section");
    reviewsSection.style.display = "none";
    adminSection.style.display = "none";
    presentedProducts.style.display = "none";
    profileEdit.style.display = "none";
    changepass.style.display = "none";
    purchaseHistory.style.display = "block"; 
}


function previewImage(event) {
const file = event.target.files[0]; // Get the first file from the selected files

if (file) {
    const reader = new FileReader();
    const previewImage = document.getElementById('profile-img');

    reader.onloadend = function () {
        previewImage.style.display = "block";
        previewImage.src = reader.result;
    }

    reader.readAsDataURL(file);
} else {
    previewImage.style.display = "none"; // Hide the preview image if no file is selected
    previewImage.src = "";
}
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
