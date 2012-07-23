<?php

/**
 * StatusController.
 *
 * @author Katsuhiro Ogawa <fivestar@nequal.jp>
 */
class Controller_Status extends Controller_Base
{
    protected $auth_actions = array('index', 'post');

    public function action_index()
    {
        $user = $this->session->get('user');
        $statuses = $this->db_manager->get('Status')
            ->fetchAllPersonalArchivesByUserId($user['id']);

        /*
        return $this->render(array(
            'statuses' => $statuses,
            'body'     => '',
            '_token'   => $this->generateCsrfToken('status/post'),
        ));
        */
        $data = array(
            'statuses' => $statuses,
            'body'     => '',
        );
        $this->template->title   = 'ホーム';
        $this->template->session = $this->session;
        $this->template->content = View::forge('status/index', $data);
    }

    public function action_post()
    {
        if (Input::method() != 'POST') {
            throw new HttpNotFoundException;
        }

        // CSRFトークンが正しいかチェック
        if ( ! \Security::check_token())
        {
        	// CSRF攻撃またはCSRFトークンの期限切れ
            Response::redirect('/account/signup');
        }

        $body = Input::post('body');

        $errors = array();

        if (!strlen($body)) {
            $errors[] = 'ひとことを入力してください';
        } else if (mb_strlen($body) > 200) {
            $errors[] = 'ひとことは200 文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $user = $this->session->get('user');
            $this->db_manager->get('Status')->insert($user['id'], $body);

            Response::redirect('/');
        }

        $user = $this->session->get('user');
        $statuses = $this->db_manager->get('Status')
            ->fetchAllPersonalArchivesByUserId($user['id']);

        /*
        return $this->render(array(
            'errors'   => $errors,
            'body'     => $body,
            'statuses' => $statuses,
            '_token'   => $this->generateCsrfToken('status/post'),
        ), 'index');
        */
        $data = array(
            'errors'   => $errors,
            'body'     => $body,
            'statuses' => $statuses,
        );
        $this->template->title   = 'ホーム';
        $this->template->session = $this->session;
        $this->template->content = View::forge('status/index', $data);
    }

    public function action_user()
    {
        $user = $this->db_manager->get('User')
            ->fetchByUserName($this->param('user_name'));
        if (!$user) {
            throw new HttpNotFoundException;
        }

        $statuses = $this->db_manager->get('Status')
            ->fetchAllByUserId($user['id']);
        
        $following = null;
        if ($this->session->isAuthenticated()) {
            $my = $this->session->get('user');
            if ($my['id'] !== $user['id']) {
                $following = $this->db_manager->get('Following')
                    ->isFollowing($my['id'], $user['id']);
            }
        }

        /*
        return $this->render(array(
            'user'      => $user,
            'statuses'  => $statuses,
            'following' => $following,
            '_token'    => $this->generateCsrfToken('account/follow'),
        ));
        */
        $data = array(
            'user'      => $user,
            'statuses'  => $statuses,
            'following' => $following,
        );
        $this->template->title   = $user['user_name'];
        $this->template->session = $this->session;
        $this->template->content = View::forge('status/user', $data);
    }

    public function action_show()
    {
        $status = $this->db_manager->get('Status')
            ->fetchByIdAndUserName($this->param('id'), $this->param('user_name'));

        if (!$status) {
            throw new HttpNotFoundException;
        }

        $this->template->title   = $status['user_name'];
        $this->template->session = $this->session;
        $this->template->content = View::forge('status/show', array('status' => $status));
    }

    public function action_signin()
    {
        if ($this->session->isAuthenticated()) {
            Response::redirect('/account');
        }

        return $this->render(array(
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signin'),
        ));
    }
}
