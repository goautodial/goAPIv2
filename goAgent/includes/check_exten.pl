#!/usr/bin/perl
use IO::Socket;

$cmd_req = $ARGV[0];

# flush after every write
#$| = 1;
eval 
	{
		local $SIG{ALRM} = sub { die 'Timed Out'; };
		alarm 3;
		# creating object interface of IO::Socket::INET modules which internally creates 
		# socket, binds and connects to the TCP server running on the specific port.
		my $socket = IO::Socket::INET->new(
			PF_INET,
			SOCK_STREAM,
			PeerHost => '127.0.0.1',
			PeerPort => '707',
			Timeout => '3600',
			Proto => 'tcp',
		) or die "ERROR in Socket Creation : $!\n";
		
		print "TCP Connection Success.\n";
		
		$socket->autoflush(1);
		
		# write on the socket to server.
		#print $socket "$cmd_req\n";
		# we can also send the data through IO::Socket::INET module,
		my $sendVal = $socket->send($cmd_req);
		print "Send to Server : $sendVal\n";
		
		# read the socket data sent by server.
		#$data = <$socket>;
		# we can also read from socket through recv()  in IO::Socket::INET
		my $retVal = $socket->recv($data, 10240);
		print "Received from Server : $data\n";
		
		#sleep (10);
		close($socket);
		undef $/; $data = <$socket>; $/ = "\n";
		alarm 0;
	};
alarm 0; # race condition protection