<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Moodle</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }

        #chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #0073aa, #005f8a);
            color: white;
            border: none;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            font-size: 26px;
            cursor: pointer;
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        #chat-button:hover {
            transform: scale(1.1);
        }

        #chat-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 350px;
            background: white;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: fadeIn 0.5s ease-in-out;
        }

        #chat-header {
            background: #0073aa;
            color: white;
            padding: 12px;
            display: flex;
            align-items: center;
            font-weight: bold;
            justify-content: space-between;
        }

        #chat-header-left {
            display: flex;
            align-items: center;
        }

        #chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        #chat-status {
            font-size: 12px;
            color: #d1e7fd;
        }

        #close-chat {
            background: white;
            border: 2px solid #0073aa;
            color: #0073aa;
            font-size: 14px;
            cursor: pointer;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        #chat-messages {
            padding: 12px;
            height: 280px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            display: flex;
            align-items: center;
            margin: 6px 0;
            padding: 10px;
            border-radius: 15px;
            max-width: 80%;
        }

        .user {
            background: #d1e7fd;
            align-self: flex-end;
            text-align: right;
        }

        .bot {
            background: #f1f1f1;
            align-self: flex-start;
            text-align: left;
            display: flex;
            align-items: center;
        }

        .bot img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 8px;
        }

        #chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        #user-input {
            flex: 1;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #send-button {
            background: #0073aa;
            color: white;
            border: none;
            padding: 6px 12px;
            margin-left: 5px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <button id="chat-button">💬</button>
    <div id="chat-container">
        <div id="chat-header">
            <div id="chat-header-left">
                <img src="https://i.imgur.com/6RK7NQp.png" alt="Bot">
                <div id="chat-title">
                    <div>Toinha Moodle</div>
                    <span id="chat-status">Online agora</span>
                </div>
            </div>
            <button id="close-chat">✖</button>
        </div>
        <div id="chat-messages"></div>
        <div id="chat-input">
            <input type="text" id="user-input" placeholder="Digite sua pergunta...">
            <button id="send-button">➤</button>
        </div>
    </div>

    
    <script>
    const chatButton = document.getElementById("chat-button");
    const chatContainer = document.getElementById("chat-container");
    const closeChat = document.getElementById("close-chat");
    const userInput = document.getElementById("user-input");
    const sendButton = document.getElementById("send-button");
    const chatMessages = document.getElementById("chat-messages");
    const chatStatus = document.getElementById("chat-status");
    
    let isBotTyping = false;

    chatButton.addEventListener("click", () => {
        chatContainer.style.display = "flex";
        
        // Se for a primeira vez que o chat é aberto, iniciar a conversa
        if (chatMessages.children.length === 0) {
            showTypingIndicator();
            
            setTimeout(() => {
                addMessage("Toinha está respondendo: " + message, "bot");
            }, 1000);
        }
    });

    closeChat.addEventListener("click", () => {
        chatContainer.style.display = "none";
    });

    sendButton.addEventListener("click", sendMessage);
    userInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") sendMessage();
    });

    function sendMessage() {
        let message = userInput.value.trim();
        if (message === "" || isBotTyping) return;
        
        addMessage(message, "user");
        userInput.value = "";
        
        // Mostrar indicador de digitação com animação
        showTypingIndicator();
        
        // Enviar mensagem para o backend
        fetch("chatbot.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "message=" + encodeURIComponent(message)
})
.then(response => response.text())
.then(data => {
    console.log("Resposta do servidor:", data); // Debug para ver a resposta no console
    hideTypingIndicator();
    displayBotMessageWithTypingEffect(data);
})
.catch(error => console.error("Erro na requisição:", error));

    }

    function addMessage(text, type) {
        let msg = document.createElement("div");
        msg.classList.add("message", type);

        if (type === "bot") {
            let img = document.createElement("img");
            img.src = "https://i.imgur.com/6RK7NQp.png";
            msg.appendChild(img);
        }

        let span = document.createElement("span");
        span.textContent = text;
        msg.appendChild(span);
        chatMessages.appendChild(msg);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Mostrar indicador de digitação com animação de pontos
    function showTypingIndicator() {
        isBotTyping = true;
        chatStatus.textContent = "Digitando";
        
        // Criar animação dos pontos (...) no indicador de digitação
        let dots = 0;
        const typingInterval = setInterval(() => {
            if (!isBotTyping) {
                clearInterval(typingInterval);
                return;
            }
            
            dots = (dots + 1) % 4;
            let dotsText = "";
            for (let i = 0; i < dots; i++) {
                dotsText += ".";
            }
            chatStatus.textContent = "Digitando" + dotsText;
        }, 500);
    }
    
    function hideTypingIndicator() {
        isBotTyping = false;
        chatStatus.textContent = "Online agora";
    }
    
    function displayBotMessageWithTypingEffect(text) {
        let msg = document.createElement("div");
        msg.classList.add("message", "bot");
        
        let img = document.createElement("img");
        img.src = "https://i.imgur.com/6RK7NQp.png";
        msg.appendChild(img);
        
        let span = document.createElement("span");
        span.textContent = "";
        msg.appendChild(span);
        
        chatMessages.appendChild(msg);
        
        // Efeito de digitação caractere por caractere com velocidade variável
        let i = 0;
        
        function typeCharacter() {
            if (i < text.length) {
                span.textContent += text.charAt(i);
                i++;
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Velocidade de digitação variável para parecer mais humano
                // Mais rápido em partes do meio, mais lento no início e fim
                let typingSpeed;
                
                if (i < 5 || i > text.length - 10) {
                    // Mais lento no início e fim da mensagem
                    typingSpeed = Math.random() * 70 + 50; // 50-120ms
                } else {
                    // Mais rápido no meio da mensagem
                    typingSpeed = Math.random() * 40 + 30; // 30-70ms
                }
                
                // Pausa mais longa em pontuação
                if (['.', '!', '?', ',', ':'].includes(text.charAt(i - 1))) {
                    typingSpeed += 300; // Pausa extra em pontuação
                }
                
                setTimeout(typeCharacter, typingSpeed);
            }
        }
        
        typeCharacter();
    }
</script>
</body>
</html>
