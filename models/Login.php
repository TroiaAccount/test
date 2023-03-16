<?php

    class Login extends Model{

        public function __construct()
        {
            $this->table = "logins";
            parent::__construct();
        }

    }
