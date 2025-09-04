function initChatbot() {
    const toggle = document.getElementById('chat-toggle');
    const widget = document.getElementById('chat-widget');
    const input = document.getElementById('chat-text');
    const sendBtn = document.getElementById('chat-send');
    const messages = document.getElementById('chat-messages');

    // Si le widget n’est pas présent sur cette page (ex: non connecté), on sort
    if (!toggle || !widget || !input || !sendBtn || !messages) {
        return;
    }

    // Empêcher les doubles initialisations (Turbo, re-renders…)
    if (widget.dataset.bound === 'true') {
        return;
    }
    widget.dataset.bound = 'true';

    const scrollToBottom = () => {
        messages.scrollTop = messages.scrollHeight;
    };

    const appendMessage = (role, text) => {
        const div = document.createElement('div');
        div.className = role === 'user' ? 'msg msg--user' : 'msg msg--bot';
        div.textContent = text;
        messages.appendChild(div);
        scrollToBottom();
    };

    const setSending = (sending) => {
        sendBtn.disabled = sending;
        input.disabled = sending;
        sendBtn.classList.toggle('is-sending', sending);
    };

    let loaderEl = null;
    const showLoader = () => {
        loaderEl = document.createElement('div');
        loaderEl.className = 'msg msg--bot msg--loader';
        loaderEl.textContent = '…';
        messages.appendChild(loaderEl);
        scrollToBottom();
    };
    const hideLoader = () => {
        if (loaderEl && loaderEl.parentNode) {
            loaderEl.parentNode.removeChild(loaderEl);
        }
        loaderEl = null;
    };

    const openWidget = () => {
        widget.style.display = 'block';
        input.focus();
    };

    const toggleWidget = () => {
        if (widget.style.display === 'none' || widget.style.display === '') {
            openWidget();
        } else {
            widget.style.display = 'none';
        }
    };

    const sendMessage = async () => {
        const text = (input.value || '').trim();
        if (!text) return;

        appendMessage('user', text);
        input.value = '';
        setSending(true);
        showLoader();

        try {
            const res = await fetch('/chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ message: text }),
            });

            if (!res.ok) {
                throw new Error(`Erreur serveur (${res.status})`);
            }

            const data = await res.json();
            hideLoader();
            appendMessage('bot', data?.reply ?? 'Désolé, aucune réponse.');
        } catch (e) {
            hideLoader();
            appendMessage('bot', "Une erreur est survenue. Réessayez plus tard.");
            // Optionnel: console.error(e);
        } finally {
            setSending(false);
        }
    };

    // Handlers
    toggle.addEventListener('click', toggleWidget);
    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
}

// Compatible DOM classique et Turbo
document.addEventListener('DOMContentLoaded', initChatbot);
document.addEventListener('turbo:load', initChatbot);
