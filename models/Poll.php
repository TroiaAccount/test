<?php

    class Poll extends Model{

        public function __construct()
        {
            $this->table = "Polls";
            parent::__construct();
        }

    }
