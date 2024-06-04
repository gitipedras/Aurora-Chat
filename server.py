import socket
import threading

def handle_client(client_socket, address):
    print(f"Accepted connection from {address}")
    
    # Send messages from messages.txt to the client
    with open('messages.txt', 'r') as file:
        for line in file:
            client_socket.send(line.encode('utf-8'))

    # Receive and broadcast messages from the client
    while True:
        message = client_socket.recv(1024)
        if not message:
            break
        with open('messages.txt', 'a') as file:
            file.write(f"{message.decode('utf-8')}\n")
        print(f"Received message: {message.decode('utf-8')}")
        # Broadcast the message to all connected clients
        for client in clients:
            if client != client_socket:
                client.send(message)

    client_socket.close()
    print(f"Connection from {address} closed.")

def main():
    host = '127.0.0.1'
    port = 8888

    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.bind((host, port))
    server_socket.listen(5)

    print(f"Server listening on {host}:{port}")

    while True:
        client_socket, address = server_socket.accept()
        client_thread = threading.Thread(target=handle_client, args=(client_socket, address))
        client_thread.start()

if __name__ == "__main__":
    main()
