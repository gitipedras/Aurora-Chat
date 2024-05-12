require 'socket'

class ChatClient
  def initialize(server, port)
    puts "Enter server:"
    server = gets.chomp
    @server = server
    @port = port
    @username = nil
    @socket = TCPSocket.new(@server, @port)
  end

  def start
    puts "username:"
    @username = $stdin.gets.chomp
    @socket.puts(@username)

    Thread.new do
      loop do
        message = @socket.gets.chomp
        puts message
      end
    end

    loop do
      message = $stdin.gets.chomp
      if message.downcase == 'exit'
        @socket.puts(message)
        break
      elsif message.downcase == '!switch'
        switch_channel
      else
        @socket.puts("#{@username}: #{message}")
      end
    end
  end

  def switch_channel
    @socket.puts('!list_channels')
    puts "Available channels:"
    loop do
      channel = @socket.gets.chomp
      break if channel.empty?
      puts "- #{channel}"
    end
    print "Enter the name of the channel to switch: "
    selected_channel = $stdin.gets.chomp
    @socket.puts("!join #{selected_channel}")
    puts "Joined channel: #{selected_channel}"
  end
end

client = ChatClient.new('localhost', 2000)
client.start
