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
          elsif message.downcase == 'get_chat_log'
            send_chat_log(client)
          else
            broadcast(message, client)
            save_to_chat_log(message)
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
  end

  def save_to_chat_log(message)
    @chat_log.puts(message)
    @chat_log.flush
  end

  def send_chat_log(client)
    @chat_log.rewind
    @chat_log.each do |line|
      client.puts line.chomp
    end
  end
end

server = ChatServer.new(2000)
server.start

