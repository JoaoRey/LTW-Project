document.addEventListener("DOMContentLoaded", function() {
  
  const button = document.querySelector("#profile-button");
  const link = document.querySelector("#login-register");
  
  button.addEventListener("mouseenter", function() {
    link.classList.add("visible");
  });
  
  button.addEventListener("mouseleave", function(event) {
    if (!link.contains(event.relatedTarget)) {
      link.classList.remove("visible");
    }
  });
  
  link.addEventListener("mouseenter", function() {
    link.classList.add("visible");
  });
  
  link.addEventListener("mouseleave", function(event) {
    if (!button.contains(event.relatedTarget)) {
      link.classList.remove("visible");
    }
  });
});
function togglePasswordVisibility(id) {
  var input = document.getElementById(id);
  var eyeOpen = input.nextElementSibling.querySelector('.eye-open');
  var eyeClosed = input.nextElementSibling.querySelector('.eye-closed');
  if (input.type === "password") {
      input.type = "text";
      eyeOpen.style.display = 'none';
      eyeClosed.style.display = 'block';
  } else {
      input.type = "password";
      eyeOpen.style.display = 'block';
      eyeClosed.style.display = 'none';
  }
}