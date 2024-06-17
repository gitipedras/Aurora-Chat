import os

# Get all file names in the /posts directory
post_files = os.listdir("posts")

# Generate HTML content for post links
post_links = ""
for filename in post_files:
    post_name = os.path.splitext(filename)[0]  # Remove the file extension
    post_name = post_name.replace("_", " ")  # Replace underscores with spaces
    post_links += f'<a href="posts/{filename}">{post_name}</a><br>\n'

# HTML template
html_template = f"""
<!DOCTYPE html>
<html>
<head>
    <title>Aurora Blog - A blog about Aurora Chat!</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>

<div class="container">

<div class="content">
<h2>About</h2>
<p>Our blog where we post posts!</p>
<p>Check out our posts below!</p>
<img src="images/profile.png" width="10%">Profile Pic.</img>
<h2>Posts</h2>
{post_links}
</div>

</div>
</body>
</html>
"""

# Write the HTML content to a file
with open("index.html", "w") as file:
    file.write(html_template)

print("[PYTHON](Success) Generated posts")
