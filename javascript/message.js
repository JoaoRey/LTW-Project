

function removeMessage() {

    
    setTimeout(function() {
        const messagesSection = document.querySelector('#messages');
        messagesSection.innerHTML = '';
    }, 5000); 
    
}
removeMessage();