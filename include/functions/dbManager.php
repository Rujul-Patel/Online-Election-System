<?php
	//PHP File For Managing Database Connections
	
	class dbManager
	{
		
		private $dbHost = "localhost";
		private $username = "rujul";
		private $password = "rujul";
		private $database = "rujul_oes";
		
		private $connection;
		private $isConnected = false;		//Whether Connection to database Server is established or not
		
		public $stay_open = false;
		
		/*****************************
			Constructor
			Opens Database Connection
		*****************************/
		function __construct()
		{
			$this->connection = new mysqli($this->dbHost,$this->username,$this->password,$this->database);
			if(mysqli_connect_error())
			{
				echo "Failed To Connect to Database Server";
				$this->isConnected = false;
			}
			else
			{
				//Connected
				$this->isConnected = true;
			}
		}
		
		
		/**
			Destructor
			Close Database Connection
		**/
		function __destruct()
		{
			if($this->isConnected && $this->stay_open = false)
			{
				$this->connection->close();
				$this->isConnected = false;
			}	
		}
			
		public function close_connection()
		{
			if($this->isConnected)
			{
				$this->connection->close();
				$this->isConnected = false;
			}
		}
		
		
		public function getConnection()
		{
			if($this->isConnected)
				return $this->connection;
			else
				return null;
		}
	}

?>