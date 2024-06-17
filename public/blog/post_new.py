def create_html_post(title, content, filename):
    html_content = f"""
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{title}</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>iLinux</h1>
            <p>An open-source blog.</p>
        </div>
        <div class="content" style="width: 900px">
            <div class="recent-posts">
                <h2>Pages</h2>
                <ul>
                <li><a href="../index.html">> Home</a></li>
                <li><a href="../pages.html">> Archive</a></li>
                <h2>Recent Posts</h2>
                
                    <li><a href="20_26may.html">News (week 20-26 may)</a></li>
                    <li><a href="chromenewtab.html">My custom new tab</a></li>
                </ul>
            </div>
            <div class="center-content">
               
<h1>{title}</h1>
{content}
        </div>

            </div>
         <!--  <div class="archive">
            <div class="aboutme">
                <h2>About Me</h2>
            <div class="aboutme">
                <img src="../images/profile.png" width="20%">
                <p>LinuxMaestroDeveloper</p>
                <a href="aboutme.html">Read More</a>
            </div>
                <h2>Archive</h2>
                <ul>
                    <li><a href="archive-january-2024.html">May 2024</a></li>
                    <li><a href="archive-february-2024.html">June 2024</a></li>
                    <li><a href="archive-march-2024.html">March 2024</a></li>
                    <li><a href="archive-april-2024.html">April 2024</a></li>
                    <li><a href="archive-may-2024.html">May 2024</a></li>
                </ul>
            -->
            </div>
        </div>
    </div>
</body>
</html>
"""

    with open(filename, 'w') as file:
        file.write(html_content)

# Example usage:
title = input("Enter post title: ")
content = input("Enter post content: ")
filename = input("Enter file name (with .html extension): ")

create_html_post(title, content, filename)
