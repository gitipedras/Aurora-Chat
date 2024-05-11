require 'socket'

class ChatServer
  def initialize(port)
    @server = TCPServer.new(port)
    @clients = []
    @chat_log = File.open("chat.txt", "a+")
    puts "Chat server started on port #{port}"
  end

  def start
    loop do
      Thread.start(@server.accept) do |client|
        @clients << client
        puts "#{client} joined the chat.."
        broadcast("New user joined the chat!", client)

        loop do
          message = client.gets.chomp
          if message.downcase == 'exit'
            client.puts "Goodbye!"
            broadcast("User left the chat", client)
            client.close
            @clients.delete(client)
            break
          else
            broadcast(message, client)
          end
        end
      end
    end
  end

  def broadcast(message, sender)
    @clients.each do |client|
      next if client == sender
      client.puts "#{sender.peeraddr[1]}: #{message}"
    end

    @chat_log.puts("#{sender.peeraddr[1]}: #{message}")
    @chat_log.flush
  end
end

server = ChatServer.new(2000)
server.start

