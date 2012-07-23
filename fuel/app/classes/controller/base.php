<?php

/**
 * Base Controller
 */
class Controller_Base extends Controller_Template
{
    protected $auth_actions;
    protected $session;
    protected $db_manager;
    
    public function before(){
        parent::before();

        $this->session  = new Session();

        // 認証が必要なアクションのチェック
        if ($this->auth_actions === true
            || (is_array($this->auth_actions) && in_array($this->request->route->action, $this->auth_actions))
        ) {
            // 認証が必要
            if ( ! $this->session->isAuthenticated())
            {
                Response::redirect('account/signin');
            }
        }

        $this->db_manager  = new DbManager();
        Config::load('db', true);
        $this->db_manager->connect('master', array(
            'dsn'      => Config::get('db.master.dsn'),
            'user'     => Config::get('db.master.user'),
            'password' => Config::get('db.master.password'),
        ));
    }
}
