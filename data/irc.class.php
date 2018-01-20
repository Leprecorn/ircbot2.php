<?php

class irc
{
	private $sock;
	private $channels = array();
	
	function __construct()
	{
		// Do nothing
		//
	}

	public function connect($server, $port)
	{
		$this->sock = fsockopen($server, $port);

		if ($this->sock === false)
		{
			$errorCode = socket_last_error();
			$errorString = socket_strerror($errorCode);
			die("Error $errorCode: $errorString".PHP_EOL);
		}
	}

	public function joinChannel($channel, $key = null)
	{
		$msg = "JOIN ".$channel;
		if ($key != null) {
			$msg .= " ".$key;
		}
		$this->send($msg);
		$this->channels[$channel] = true;
	}

	public function partChannel($channel)
	{
		if ($this->channels[$channel] = true) {
			$this->send('PART '.$channel);
		} else {
			output('Cannot quit channel '.$channel.' , Reason: Not on that channel!' ,'warning');
		}
	}

	public function timedOut()
	{
		return stream_get_meta_data($this->sock)['timed_out'];
		//
	}

	public function setTimeout($secs, $ms = 0)
	{
		stream_set_timeout($this->sock, $secs, $ms);
		//
	}

	public function register($nick, $user, $real)
	{
		$this->send("NICK $nick");
        $this->send("USER $user * 8 :$real");
	}

	public function send($message)
	{
		fputs($this->sock, $message . "\r\n");
		output(">>> " . $message);
	}

	public function privmsg($dest, $message)
	{
		$this->send("PRIVMSG ".$dest." :".$message);
		//
	}

	private function pong($data)
	{
        $this->send("PONG " . $data);
        //
    }

	public function recv()
	{
        $raw = trim(fgets($this->sock, 4096));
        $ex = array_pad(explode(' ', $raw), 30, null);
        if ($ex[0] === 'PING') {
            $this->pong($ex[1]);
            return $this->recv();
        } else {
            return $raw;
        }
    }

    public function die($message = "Bye!")
    {
    	$this->send("QUIT :".$message);
    	//
    }
}

?>