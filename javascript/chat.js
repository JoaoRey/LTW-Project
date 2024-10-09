const chatContainer = document.getElementById('chat-container');

if(chatContainer) {
    
    
    function loadMessages(flag = false) {
        console.log('Carregando mensagens...');
        
        const receiverId = document.getElementById('receiver-id').value;
        const itemId = document.getElementById('item-id').value;
        
        if(receiverId && itemId) { 
            fetch(`/../actions/get_messages.php?owner_id=${receiverId}&item_id=${itemId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar mensagens: ' + response.statusText);
                }
                return response.json(); 
            })
            .then(messages => {
                console.log('Mensagens recebidas:', messages);
                
                drawChat(receiverId, itemId, messages);
            })
            .catch(error => {
                console.error('Erro ao carregar mensagens:', error);
            });
        }
        
    }
    
    function drawChat(receiverId, itemId, messages,flag) {
        receiverId = parseInt(receiverId); 
    
        const messagesContainer = document.getElementById('messages-container');
        messagesContainer.innerHTML = '';
    
        messages.forEach(message => {
            console.log('message:', message);
            console.log('message.sendername:', message.SenderName);
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message');
            messageDiv.classList.add(message.SenderId !== receiverId ? 'sent' : 'received');
    
            const senderName = message.SenderId === receiverId ? message.SenderName : 'Você';
            console.log('senderName:', senderName);
            const messageContent = document.createElement('p');
            messageContent.textContent = `${message.CommunicationText}`; 
            const messageDate = document.createElement('span');
            messageDate.textContent = message.SendDate;
    
            messageDiv.appendChild(messageContent);
            messageDiv.appendChild(messageDate);
    
            messagesContainer.appendChild(messageDiv);
        });
        if(!flag){
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    function sendMessage(receiverId, message) {
        const formData = new FormData();
        formData.append('receiverId', receiverId);
        formData.append('message-input', message);
        formData.append('item-id', document.getElementById('item-id').value);
    
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
    
        fetch('../actions/send_message.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao enviar mensagem: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log(data.message); 
                loadMessages();
            } else {
                console.error(data.error); 
            }
        })
        .catch(error => {
            console.error('Erro ao enviar mensagem:', error);
        });
    }
    
    
    document.getElementById('message-form').addEventListener('submit', function(event) {
        event.preventDefault(); 
        
        const formData = new FormData(this);
        const message = formData.get('message-input');
        const receiverId = document.getElementById('receiver-id').value;
        
        if (message.trim() !== '') {
            sendMessage(receiverId, message);
            document.getElementById('message-input').value = '';
        } else {
            alert('Por favor, insira uma mensagem antes de enviar.');
        }
    });
    
    setInterval(loadMessages(true), 5000);
    
}