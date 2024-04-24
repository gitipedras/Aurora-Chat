echo "Port 5555 will be killed"

read -p "Press ENTER to kill all processes on port 555"
sudo lsof -i :5555
