<?php

namespace IonAuth\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class LoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        if (!($this->ionAuth->loggedIn())) {

            session()->set('redirect_url', current_url());

            return redirect()->to('/login');            
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
