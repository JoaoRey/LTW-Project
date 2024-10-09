window.print();
window.addEventListener('afterprint', function () {window.location.href = document.referrer; });