import tkinter as tk
import socket
import threading

class ChatClient:
    def __init__(self, host, port):
        self.host = host
        self.port = port
        self.socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.socket.connect((self.host, self.port))

        # GUI setup
        self.root = tk.Tk()
        self.root.title("Chat Client")
        self.message_frame = tk.Frame(self.root)
        self.message_frame.pack(padx=10, pady=10)
        self.message_entry = tk.Entry(self.message_frame, width=50)
        self.message_entry.pack(side=tk.LEFT)
        self.send_button = tk.Button(self.message_frame, text="Send", command=self.send_message)
        self.send_button.pack(side=tk.RIGHT)
        self.message_text = tk.Text(self.root, width=60, height=20)
        self.message_text.pack(padx=10, pady=10)

        # Receive messages in a separate thread
        receive_thread = threading.Thread(target=self.receive_messages)
        receive_thread.start()

        self.root.mainloop()

    def send_message(self):
        message = self.message_entry.get()
        self.socket.sendall(message.encode('utf-8'))
        self.message_entry.delete(0, tk.END)

    def receive_messages(self):
        while True:
            try:
                message = self.socket.recv(1024).decode('utf-8')
                if message:
                    self.message_text.insert(tk.END, message + '\n')
                    self.message_text.see(tk.END)  # Scroll to the end of the text widget
            except ConnectionResetError:
                break

def main():
    host = '127.0.0.1'
    port = 8888
    ChatClient(host, port)

if __name__ == "__main__":
    main()
