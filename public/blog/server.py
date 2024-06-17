import http.server
import socketserver

# Define the port number
PORT = 80

# Define the directory containing the index.html file
DIRECTORY = '.'

# Define the server handler
class CustomHandler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=DIRECTORY, **kwargs)

# Create the server
with socketserver.TCPServer(('ilinux.local', PORT), CustomHandler) as httpd:
    print(f'Server started on port {PORT}...')
    try:
        # Start the server
        httpd.serve_forever()
    except KeyboardInterrupt:
        # Handle keyboard interrupt (Ctrl+C) to gracefully stop the server
        print('\nServer stopped.')
