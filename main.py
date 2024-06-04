from flask import Flask, request, jsonify
from flask import Flask, render_template
from flask_jwt_extended import JWTManager, create_access_token
from werkzeug.security import generate_password_hash, check_password_hash
from flask import Flask, render_template, request, redirect, url_for

def read_users():
    users = {}
    with open('users.txt', 'r') as file:
        for line in file:
            username, password = line.strip().split('::')
            users[username] = {'username': username, 'password': password}
    return users

# Function to write user data to file
def write_users(users):
    with open('users.txt', 'w') as file:
        for user in users.values():
            file.write(f"{user['username']}::{user['password']}\n")

# Load users from file
users = read_users()

def parse_messages():
    messages = []
    with open('messages.txt', 'r') as f:
        for line in f:
            parts = line.split('::')
            if len(parts) == 2:
                username, message = parts
                messages.append((username.strip(), message.strip()))
    return messages

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your-secret-key'
jwt = JWTManager(app)

@app.route('/')
def index():
    return render_template('index.html')

# Function to read user data from file
def read_users():
    users = {}
    with open('users.txt', 'r') as file:
        for line in file:
            username, password = line.strip().split('::')
            users[username] = {'username': username, 'password': password}
    return users

# Function to write user data to file
def write_users(users):
    with open('users.txt', 'w') as file:
        for user in users.values():
            file.write(f"{user['username']}::{user['password']}\n")

# Load users from file
users = read_users()

def check_credentials(username, password):
    # Read usernames and hashed passwords from the users.txt file
    with open('users.txt', 'r') as f:
        for line in f:
            stored_username, stored_password_hash = line.strip().split('::')
            if username == stored_username and check_password_hash(stored_password_hash, password):
                return True
    return False

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']

        # Check if the username exists and the password matches
        if check_credentials(username, password):
            resp = redirect(url_for('webapp'))
            resp.set_cookie('username', username)
            return resp
        else:
            # Render the login form again with an error message
            return render_template('login.html', error='Invalid username or password')
    else:
        return render_template('login.html')
        
@app.route('/webapp')
def webapp():
    # Read messages from the messages.txt file
    messages = []
    with open('messages.txt', 'r') as f:
        for line in f:
            messages.append(line.strip())

    # Render the webapp.html template and pass the messages to it
    return render_template('webapp.html', messages=messages)

def check_credentials(username, password):
    # Read usernames and hashed passwords from the users.txt file
    with open('users.txt', 'r') as f:
        for line in f:
            stored_username, stored_password_hash = line.strip().split('::')
            if username == stored_username and check_password_hash(stored_password_hash, password):
                return True
    return False



@app.route('/signup', methods=['GET', 'POST'])
def signup():
    if request.method == 'POST':
        # Get username and password from the form
        username = request.form['username']
        password = request.form['password']
        
        # Hash the password before storing it
        hashed_password = generate_password_hash(password)
        
        # Store the username and hashed password in your database
        # For simplicity, let's just print them for now
        print("Username:", username)
        print("Hashed Password:", hashed_password)
        
        # Open users.txt in append mode and write the user's information
        with open('users.txt', 'a') as f:
            f.write(f"{username}::{hashed_password}\n")
        
        return redirect('/login')
    
    # If the request method is GET, render the signup.html template
    return render_template('signup.html')

@app.route('/send', methods=['POST'])
def send_message():
    # Retrieve the username from the cookie
    username = request.cookies.get('username')
    if not username:
        return redirect('/login')  # Redirect to login page if username is not found in the cookie

    # Get the message from the form
    message = request.form['message']

    # Save the message along with the username in messages.txt
    with open('messages.txt', 'a') as f:
        f.write(f"{username}: {message}\n")

    return redirect('/webapp')


@app.route('/signup/success')
def signup_success():
    return "User created successfully!"

if __name__ == '__main__':
    app.run(debug=True)
