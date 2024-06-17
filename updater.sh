#!/bin/bash

echo "--- > Aurora Updater < ---"

cp -r Aurora-Chat/public/blog blog-old

rm -rf Aurora-Chat
git clone https://github.com/gitipedras/Aurora-Chat.git

echo "Update complete!"
sleep 0.25


echo "Rename 'blog-old' to 'blog'"
mv blog-old blog
sleep 0.5

echo "Updating blog..."
mv blog Aurora-Chat/public/blog
sleep 0.5

echo "You are now using the latest version from 'https://github.com/gitipedras/Aurora-Chat'"

