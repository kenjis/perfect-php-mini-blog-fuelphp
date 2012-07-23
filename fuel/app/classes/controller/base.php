<?php

/**
 * Base Controller
 */
class Controller_Base extends Controller_Template
{
    protected $auth_actions; // @TODO
    protected $session;
    protected $db_manager;
    
    public function before(){
        parent::before();

        $this->session  = new Session();

        $this->db_manager  = new DbManager();
        Config::load('db', true);
        $this->db_manager->connect('master', array(
            'dsn'      => Config::get('db.master.dsn'),
            'user'     => Config::get('db.master.user'),
            'password' => Config::get('db.master.password'),
        ));
    }
}
