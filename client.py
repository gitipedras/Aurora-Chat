import tkinter as tk
from tkinter import ttk, scrolledtext, messagebox, simpledialog
import socket
import threading

class ClientApp:
    def __init__(self):
        self.root = tk.Tk()
        self.root.title("PyConvo v0.1.6")
        self.root.geometry("400x600")

        self.style = ttk.Style()
        self.style.configure("TFrame", background="#f0f0f0")
        self.style.configure("TLabel", background="#f0f0f0")
        self.style.configure("TButton", background="#007bff", foreground="#ffffff")

        self.frame_username = ttk.Frame(self.root)
        self.frame_username.pack(pady=10)

        self.label_username = ttk.Label(self.frame_username, text="Username:")
        self.label_username.grid(row=0, column=0, padx=5, pady=5)

        self.username_entry = ttk.Entry(self.frame_username, width=30)
        self.username_entry.grid(row=0, column=1, padx=5, pady=5)

        self.frame_chat = ttk.Frame(self.root)
        self.frame_chat.pack(pady=10)

        self.chat_history = scrolledtext.ScrolledText(self.frame_chat, width=40, height=20, wrap=tk.WORD)
        self.chat_history.grid(row=0, column=0, padx=5, pady=5, columnspan=2)

        self.message_var = tk.StringVar()
        self.message_entry = ttk.Entry(self.frame_chat, textvariable=self.message_var, width=30)
        self.message_entry.grid(row=1, column=0, padx=5, pady=5)

        self.send_button = ttk.Button(self.frame_chat, text="Send", command=self.send_message)
        self.send_button.grid(row=1, column=1, padx=5, pady=5)

        self.change_server_button = ttk.Button(self.root, text="Change Server", command=self.change_server)
        self.change_server_button.pack(pady=10)

        self.client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.server_address = ("127.0.0.1", 5555)
        self.connect_to_server()

        self.username = None

        self.receive_thread = threading.Thread(target=self.receive_messages)
        self.receive_thread.daemon = True  # Make the receive thread a daemon thread
        self.receive_thread.start()

        self.root.mainloop()

    def connect_to_server(self):
        try:
            self.client_socket.connect(self.server_address)
        except ConnectionRefusedError:
            messagebox.showerror("Error", "Connection to server failed.")
            self.root.destroy()  # Close the application if connection fails

    def receive_messages(self):
        while True:
            try:
                message = self.client_socket.recv(1024).decode("utf-8")
                if message:
                    self.chat_history.insert(tk.END, f"{message}\n")
                    self.chat_history.see(tk.END)  # Scroll to the end of the chat history
            except ConnectionResetError:
                print("Connection with server has been reset.")
                break

    def send_message(self):
        username = self.username_entry.get()
        message = self.message_var.get()

        if not username:
            messagebox.showerror("Error", "Please enter a username.")
            return
        if not message:
            messagebox.showerror("Error", "Please enter a message.")
            return

        full_message = f"{username}: {message}"
        self.client_socket.send(full_message.encode("utf-8"))
        self.message_var.set("")  # Clear the message entry after sending

    def change_server(self):
        new_server_address = simpledialog.askstring("Change Server", "Enter new server address (IP:Port):")
        if new_server_address:
            host, port = new_server_address.split(":")
            self.server_address = (host, int(port))
            self.client_socket.close()  # Close existing connection
            self.client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            self.connect_to_server()
            messagebox.showinfo("Server Changed", f"Connected to new server: {new_server_address}")

if __name__ == "__main__":
    app = ClientApp()
