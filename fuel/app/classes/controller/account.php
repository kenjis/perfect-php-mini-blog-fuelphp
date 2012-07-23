<?php

/**
 * AccountController.
 *
 * @author Katsuhiro Ogawa <fivestar@nequal.jp>
 */
//include_once(dirname(__FILE__).'/session.php');
class Controller_Account extends Controller_Template
{
    protected $auth_actions = array('index', 'signout', 'follow');
    protected $session = null;


    public function before(){
        parent::before();
        $this->session  = new Session();
        $this->db_manager  = new DbManager();
        $this->db_manager->connect('master', array(
            'dsn'      => 'mysql:dbname=mini_blog;host=localhost;charset=utf8',
            'user'     => 'root',
            'password' => '',
        ));
    }


    public function action_signup()
    {
        if ($this->session->isAuthenticated()) {
            Response::redirect('/account');
            //return $this->redirect('/account');
        }

        $data = array(
           'user_name'=>'',
           'password'=>'',
           'session' => $this->session,
        );
        $this->template->title   = 'アカウント登録';
        $this->template->session = $this->session;
        $this->template->content = View::forge('account/signup', $data);

/*
        return $this->render(array(
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signup'),
        ));
*/
    }

    public function action_register()
    {
        if ($this->session->isAuthenticated()) {
            Response::redirect('/account');
        }

        if (Input::method() != 'POST') {
            throw new HttpNotFoundException;;
        }

/*
        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signup', $token)) {
            return $this->redirect('/account/signup');
        }
*/
        // CSRFトークンが正しいかチェック
        if ( ! \Security::check_token())
        {
        	// CSRF攻撃またはCSRFトークンの期限切れ
            Response::redirect('/account/signup');
        }
    
        $user_name = Input::post('user_name');
        $password = Input::post('password');
//        $this->request->getPost('user_name');
//        $password = $this->request->getPost('password');

        $errors = array();

        if (!strlen($user_name)) {
            $errors[] = 'ユーザIDを入力してください';
        } else if (!preg_match('/^\w{3,20}$/', $user_name)) {
            $errors[] = 'ユーザIDは半角英数字およびアンダースコアを3 ～ 20 文字以内で入力してください';
        } else if (!$this->db_manager->get('User')->isUniqueUserName($user_name)) {
            $errors[] = 'ユーザIDは既に使用されています';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        } else if (4 > strlen($password) || strlen($password) > 30) {
            $errors[] = 'パスワードは4 ～ 30 文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $this->db_manager->get('User')->insert($user_name, $password);
            $this->session->setAuthenticated(true);

            $user = $this->db_manager->get('User')->fetchByUserName($user_name);
            $this->session->set('user', $user);

            Response::redirect('/');
        }


        $data = array(
           'user_name'=>$user_name,
           'password'=>$password,
           'errors'    => $errors,
           'session' => $this->session,
        );
        $this->template->title   = 'アカウント登録';
        $this->template->session = $this->session;
        $this->template->content = View::forge('account/signup', $data);
/*
        return $this->render(array(
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signup'),
        ), 'signup');
*/
    }

    public function action_index()
    {
        $user = $this->session->get('user');
        $followings = $this->db_manager->get('User')
            ->fetchAllFollowingsByUserId($user['id']);

        /*
        return $this->render(array(
            'user'       => $user,
            'followings' => $followings,
        ));
        */
        $data = array(
                   'user'       => $user,
                   'followings' => $followings,
        );
        $this->template->title   = 'アカウント';
        $this->template->session = $this->session;
        $this->template->content = View::forge('account/index', $data);
    }

    public function action_signin()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        return $this->render(array(
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signin'),
        ));
    }

    public function action_authenticate()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signin', $token)) {
            return $this->redirect('/account/signin');
        }

        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        $errors = array();

        if (!strlen($user_name)) {
            $errors[] = 'ユーザIDを入力してください';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors) === 0) {
            $user_repository = $this->db_manager->get('User');
            $user = $user_repository->fetchByUserName($user_name);

            if (!$user
                || ($user['password'] !== $user_repository->hashPassword($password))
            ) {
                $errors[] = 'ユーザIDかパスワードが不正です';
            } else {
                $this->session->setAuthenticated(true);
                $this->session->set('user', $user);

                return $this->redirect('/');
            }
        }

        return $this->render(array(
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signin'),
        ), 'signin');
    }

    public function action_signout()
    {
        $this->session->clear();
        $this->session->setAuthenticated(false);

        return $this->redirect('/account/signin');
    }

    public function action_follow()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $following_name = $this->request->getPost('following_name');
        if (!$following_name) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/follow', $token)) {
            return $this->redirect('/user/' . $following_name);
        }

        $follow_user = $this->db_manager->get('User')
            ->fetchByUserName($following_name);
        if (!$follow_user) {
            $this->forward404();
        }

        $user = $this->session->get('user');

        $following_repository = $this->db_manager->get('Following');
        if ($user['id'] !== $follow_user['id'] 
            && !$following_repository->isFollowing($user['id'], $follow_user['id'])
        ) {
            $following_repository->insert($user['id'], $follow_user['id']);
        }

        return $this->redirect('/account');
    }
}
