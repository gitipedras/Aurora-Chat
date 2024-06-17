const messageText = message.textContent.trim();
console.log("Original text:", messageText);

// Bold and Monospace
formattedText = formattedText.replace(/\*\*([^*]+)\*\*/g, '<code><b>$1</b></code>');
console.log("Formatted text:", formattedText);

message.innerHTML = formattedText;
