document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("chat-toggle");
    const widget = document.getElementById("chat-widget");
    const messages = document.getElementById("chat-messages");
    const input = document.getElementById("chat-text");
    const sendBtn = document.getElementById("chat-send");

    if (!toggle || !widget) return; // sécurité si pas connecté
    widget.style.display = "none";
    // Ouvrir / fermer le chat
    toggle.addEventListener("click", () => {
        widget.style.display = widget.style.display === "none" ? "flex" : "none";
    });

    function addMessage(text, sender) {
        const msg = document.createElement("div");
        msg.textContent = text;
        msg.style.margin = "5px 0";
        msg.style.padding = "5px 10px";
        msg.style.borderRadius = "10px";
        msg.style.maxWidth = "80%";
        msg.style.background = sender === "user" ? "#4f46e5" : "#818cf8";
        msg.style.alignSelf = sender === "user" ? "flex-end" : "flex-start";
        messages.appendChild(msg);
        messages.scrollTop = messages.scrollHeight;
    }

    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        addMessage(text, "user");
        input.value = "";

        const loading = document.createElement("div");
        loading.classList.add("typing-indicator");
        loading.innerHTML = "<span></span><span></span><span></span>";
        messages.appendChild(loading);
        messages.scrollTop = messages.scrollHeight;

        try {
            const response = await fetch("/chatbot", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ message: text })
            });

            const data = await response.json();

            messages.removeChild(loading);
            addMessage(data.reply, "bot");

        } catch (error) {
            messages.removeChild(loading);
            addMessage("⚠️ Erreur de connexion au chatbot.", "bot");
        }
    }

    sendBtn.addEventListener("click", sendMessage);
    input.addEventListener("keypress", (e) => {
        if (e.key === "Enter") sendMessage();
    });
});
