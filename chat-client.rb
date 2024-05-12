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
    Gtk.init

    dialog = Gtk::Dialog.new(title: "Server IP Address", parent: nil, flags: [:modal, :destroy_with_parent])
    dialog.add_button("_OK", Gtk::ResponseType::OK)
    dialog.add_button("_Cancel", Gtk::ResponseType::CANCEL)

    entry = Gtk::Entry.new
    entry.text = "localhost"
    entry.activates_default = true
    dialog.vbox.add(Gtk::Label.new("Enter the IP address of the server:"))
    dialog.vbox.add(entry)
    dialog.show_all

    dialog.run do |response|
      if response == Gtk::ResponseType::OK
        @server = entry.text
        dialog.destroy
        start_chat
      else
        dialog.destroy
        Gtk.main_quit
      end
    end

    Gtk.main
  end

  # Rest of the code remains unchanged...
end

client = ChatClient.new
client.start

