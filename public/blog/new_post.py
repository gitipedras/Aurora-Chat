import tkinter as tk
from tkinter import messagebox
from tkinter import filedialog
import re

def create_html_post():
    title = title_entry.get()
    if not title:
        messagebox.showerror("Error", "Title cannot be empty!")
        return
    
    # Replace spaces and remove invalid filename characters
    valid_filename = re.sub(r'[^\w\s-]', '', title).strip().replace(' ', '_') + ".html"
    
    # Automatically suggest the filename
    filename = filedialog.asksaveasfilename(initialfile=valid_filename, defaultextension=".html", filetypes=[("HTML files", "*.html")])
    
    if filename:
        content = content_text.get("1.0", tk.END)
        html_content = f"""
<!DOCTYPE html>
<html>
<head>
    <title>{title}</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

<div class="container">
<a href="../index.html"><- Back to Home</a>
<div class="content">
<h1>{title}</h1>
{content}
</div>

</div>
</body>
</html>
"""

        with open(filename, 'w') as file:
            file.write(html_content)
        messagebox.showinfo("Success", f"HTML file '{filename}' created successfully!")

# Create the main window
root = tk.Tk()
root.title("HTML Post Generator")

# Create and place widgets
title_label = tk.Label(root, text="Post Title:")
title_label.grid(row=0, column=0, padx=5, pady=5)

title_entry = tk.Entry(root, width=50)
title_entry.grid(row=0, column=1, columnspan=2, padx=5, pady=5)

content_label = tk.Label(root, text="Post Content:")
content_label.grid(row=1, column=0, padx=5, pady=5)

content_text = tk.Text(root, width=50, height=10)
content_text.grid(row=1, column=1, columnspan=2, padx=5, pady=5)

generate_button = tk.Button(root, text="Generate HTML", command=create_html_post)
generate_button.grid(row=2, column=1, padx=5, pady=5)

exit_button = tk.Button(root, text="Exit", command=root.quit)
exit_button.grid(row=2, column=2, padx=5, pady=5)

# Start the Tkinter event loop
root.mainloop()
