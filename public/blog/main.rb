def postdel(posttodelete)
	system("rm posts/#{posttodelete}.html")
end


while true
puts "Actions: newpost, update"

printf "action:"
action = gets.chomp

if action == "newpost"
	system("python3 new_post.py")

elsif action == "update"
	system("python3 update-posts.py")
elsif action == "quit"
	exit
	
elsif action == "postdel"
	printf "Enter post to delete(excluding .HTML):"
	posttodelete = gets.chomp

	puts postdel(posttodelete)
	

else
	puts "Invalid action"
end
end