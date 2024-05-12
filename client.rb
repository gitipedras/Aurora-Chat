require 'socket'

class ChatClient
  def initialize(server, port)
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
      @socket.puts("#{@username}: #{message}")
      break if message.downcase == 'exit'
    end
  end
end

client = ChatClient.new('localhost', 2000)
client.start

