const urlParams = new URLSearchParams(window.location.search);
    const channel = urlParams.get('channel') || 'general';
    
    const socket = io({ query: { channel } });
    const form = document.getElementById('form');
    const input = document.getElementById('input');
    const messages = document.getElementById('messages');
    const usernameInput = document.getElementById('username');
    const setUsernameButton = document.getElementById('set-username-button');
    let currentUsername = null;

    setUsernameButton.addEventListener('click', function() {
      const username = usernameInput.value.trim();
      if (username) {
        currentUsername = username;
        usernameInput.disabled = true;
        setUsernameButton.disabled = true;
        input.disabled = false;
        document.getElementById('send-button').disabled = false;
        socket.emit('set username', username);
      }
    });

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      if (input.value && currentUsername) {
        socket.emit('chat message', { username: currentUsername, message: input.value });
        input.value = '';
      }
    });

    socket.on('chat message', function(data) {
      const item = document.createElement('li');
      item.textContent = `${data.username}: ${data.message}`;
      messages.prepend(item); // Use prepend to add new messages at the top
      messages.scrollTop = messages.scrollHeight; // Keep the scroll position at the bottom
    });