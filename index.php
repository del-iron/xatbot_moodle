<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Moodle</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }

        /* Botão flutuante */
        #chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #0073aa;
            color: white;
            border: none;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* Caixa do Chat */
        #chat-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 300px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Animação de fade-in */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cabeçalho do Chat */
        #chat-header {
            background: #0073aa;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }

        /* Botão de fechar */
        #close-chat {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        /* Mensagens */
        #chat-messages {
            padding: 10px;
            height: 250px;
            overflow-y: auto;
        }

        .message {
            margin: 5px 0;
            padding: 8px;
            border-radius: 5px;
            max-width: 80%;
            opacity: 0;
            animation: fadeInMessage 0.5s forwards;
        }

        @keyframes fadeInMessage {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user { background: #d1e7fd; align-self: flex-end; text-align: right; }
        .bot { background: #f1f1f1; align-self: flex-start; text-align: left; }

        /* Input e Botão */
        #chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        #user-input {
            flex: 1;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        #send-button {
            background: #0073aa;
            color: white;
            border: none;
            padding: 5px 10px;
            margin-left: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Botão Flutuante -->
    <button id="chat-button">💬</button>

    <!-- Chatbot -->
    <div id="chat-container">
        <div id="chat-header">
            🤖 Chatbot Moodle
            <button id="close-chat">❌</button>
        </div>
        <div id="chat-messages"></div>
        <div id="chat-input">
            <input type="text" id="user-input" placeholder="Digite sua resposta...">
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

        let userName = "";

        chatButton.addEventListener("click", () => {
            chatContainer.style.display = "flex";
            if (!userName) {
                askUserName();
            }
        });

        closeChat.addEventListener("click", () => {
            chatContainer.style.display = "none";
        });

        sendButton.addEventListener("click", sendMessage);
        userInput.addEventListener("keypress", (e) => {
            if (e.key === "Enter") sendMessage();
        });

        function askUserName() {
            addMessage("Olá! Sou sua assistente digital, me chamo Toinha, e estou pronta para tornar seu dia mais fácil. Vamos começar, Qual seu nome?", "bot");
        }

        function sendMessage() {
            let message = userInput.value.trim();
            if (message === "") return;

            if (!userName) {
                userName = message;
                addMessage("Você: " + message, "user");
                setTimeout(() => {
                    addMessage(`Prazer, ${userName}! Como posso te ajudar no Moodle?`, "Toinha:");
                }, 1000);
                userInput.value = "";
                return;
            }

            addMessage(`${userName}: ${message}`, "user");
            userInput.value = "";

            fetch("chatbot.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `message=${encodeURIComponent(message)}&user=${encodeURIComponent(userName)}`
            })
            .then(response => response.text())
            .then(data => {
                setTimeout(() => {
                    addMessage("Toinha: " + data, "bot");
                    speak(data);
                }, 1000);
            });
        }

        function addMessage(text, type) {
            let msg = document.createElement("div");
            msg.classList.add("message", type);
            msg.textContent = text;
            chatMessages.appendChild(msg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function speak(text) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = "pt-BR";
            speechSynthesis.speak(utterance);
        }
    </script>

</body>
</html>
