<?php

    class Answer extends Model{

        public function __construct()
        {
            $this->table = "answers";
            parent::__construct();
        }

    }
