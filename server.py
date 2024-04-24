import socket
import threading

class Server:
    def __init__(self):
        self.host = "127.0.0.1"
        self.port = 5555
        self.server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.server_socket.bind((self.host, self.port))
        self.connections = []

    def start(self):
        self.server_socket.listen()
        print("Server is running on host 127.0.0.1 and on port 5555")
        print("PyConvo version v0.1.6")
        while True:
            client_socket, client_address = self.server_socket.accept()
            print(f"New connection from {client_address}")
            self.connections.append(client_socket)
            client_thread = threading.Thread(target=self.handle_client, args=(client_socket,))
            client_thread.start()

    def handle_client(self, client_socket):
        while True:
            try:
                message = client_socket.recv(1024).decode("utf-8")
                if message:
                    print(f"Received message: {message}")
                    self.broadcast(message)
            except ConnectionResetError:
                print("Client connection closed.")
                self.connections.remove(client_socket)
                break

    def broadcast(self, message):
        for connection in self.connections:
            try:
                connection.send(message.encode("utf-8"))
            except ConnectionResetError:
                print("Error broadcasting message to client.")

if __name__ == "__main__":
    server = Server()
    server.start()


