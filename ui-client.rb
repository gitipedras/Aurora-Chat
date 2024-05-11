require 'gtk3'
require 'socket'

class ChatClient
  def initialize
    @server = nil
    @port = 2000
    @username = nil
    @socket = nil
  end

  def start
    dialog = Gtk::Dialog.new(title: "Server IP Address", parent: nil, flags: [:modal, :destroy_with_parent])
    dialog.add_button("_OK", Gtk::ResponseType::OK)
    dialog.add_button("_Cancel", Gtk::ResponseType::CANCEL)

    entry = Gtk::Entry.new
    entry.set_text("localhost")
    entry.set_activates_default(true)
    dialog.vbox.add(Gtk::Label.new("Enter the IP address of the server:"))
    dialog.vbox.add(entry)
    dialog.show_all

    dialog.run do |response|
      if response == Gtk::ResponseType::OK
        @server = entry.text
        dialog.destroy
      else
        dialog.destroy
        return
      end
    end

    @window = Gtk::Window.new
    @window.set_title("Chat Client")
    @window.set_default_size(400, 300)

    @vbox = Gtk::Box.new(:vertical, 5)
    @window.add(@vbox)

    @textview = Gtk::TextView.new
    @textview.set_editable(false)
    @vbox.pack_start(@textview, :expand => true, :fill => true, :padding => 5)

    @entry = Gtk::Entry.new
    @vbox.pack_start(@entry, :expand => false, :fill => false, :padding => 5)

    @entry.signal_connect("activate") do
      message = @entry.text
      @socket.puts("#{@username}: #{message}")
      append_textview("#{@username}: #{message}", true)
      @entry.text = ""
    end

    @window.signal_connect("delete-event") do
      @socket.close
      Gtk::main_quit
    end

    @window.show_all

    puts "Enter your username:"
    @username = $stdin.gets.chomp

    @socket = TCPSocket.new(@server, @port)
    @socket.puts(@username)

    Thread.new do
      loop do
        message = @socket.gets.chomp
        append_textview(message, false)
      end
    end

    Gtk.main
  end

  def append_textview(message, is_sent_by_user)
    buffer = @textview.buffer
    if is_sent_by_user
      buffer.text = buffer.text + "You: #{message}\n"
    else
      buffer.text = buffer.text + message + "\n"
    end
  end
end

client = ChatClient.new
client.start

