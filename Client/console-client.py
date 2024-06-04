import socket
import threading

class ChatClient:
    def __init__(self, host, port, username):
        self.host = host
        self.port = port
        self.username = username

        self.socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.socket.connect((self.host, self.port))

        threading.Thread(target=self.receive_messages).start()

        self.send_messages()

    def send_messages(self):
        while True:
            message = input()
            if message.lower() == 'exit':
                break
            self.socket.send(f"{self.username}: {message}".encode('utf-8'))

        self.socket.close()

    def receive_messages(self):
        while True:
            try:
                message = self.socket.recv(1024).decode('utf-8')
                if not message:
                    break
                print(message)
            except ConnectionResetError:
                break

def main():
    host = '127.0.0.1'
    port = 8888
    username = input("Enter your username: ")
    ChatClient(host, port, username)

if __name__ == "__main__":
    main()
